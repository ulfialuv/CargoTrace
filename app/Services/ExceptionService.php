<?php

namespace App\Services;

use App\Models\ExceptionHistory;
use App\Models\Shipment;
use App\Models\ShipmentException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Pengelolaan exception: pembuatan, perubahan status + histori, dan deteksi delay otomatis. */
class ExceptionService
{
    public const TYPES = [
        'CUSTOMS_HOLD', 'WEATHER_DELAY', 'PORT_CONGESTION',
        'DAMAGED', 'MISROUTED', 'DOCUMENTATION_ISSUE',
    ];

    public const SEVERITIES = ['LOW', 'MEDIUM', 'HIGH'];

    public const STATUSES = ['OPEN', 'INVESTIGATING', 'RESOLVED'];

    /** Penanda exception yang dibuat otomatis (sama dengan modul web). */
    public const AUTO_DELAY = '[AUTO-DELAY]';

    public function __construct(private EtaService $eta)
    {
    }

    public function create(array $data, ?int $userId, ?string $note = null): ShipmentException
    {
        $data['status']      = $data['status'] ?? 'OPEN';
        $data['reported_by'] = $userId;
        $data['resolved_at'] = $data['status'] === 'RESOLVED' ? now() : null;

        return DB::transaction(function () use ($data, $userId, $note) {
            $exception = ShipmentException::create($data);

            $this->record($exception, $userId, 'CREATED', null, $exception->status, $note ?? 'Pengecualian dilaporkan.');

            return $exception;
        });
    }

    public function update(ShipmentException $exception, array $data, ?int $userId, ?string $note = null): ShipmentException
    {
        $old = $exception->status;

        if (isset($data['status'])) {
            // RESOLVED mencatat waktu penyelesaian; dibuka lagi -> dikosongkan.
            $data['resolved_at'] = $data['status'] === 'RESOLVED' ? ($exception->resolved_at ?? now()) : null;
        }

        DB::transaction(function () use ($exception, $data, $userId, $note, $old) {
            $exception->update($data);

            $changed = $exception->status !== $old;

            $this->record($exception, $userId, $changed ? 'STATUS_CHANGED' : 'UPDATED', $old, $exception->status, $note);
        });

        return $exception->fresh();
    }

    /**
     * Sinkronkan exception delay otomatis untuk satu shipment:
     *  - terlambat  -> buat / perbarui exception [AUTO-DELAY] (severity dari EtaService)
     *  - tidak lagi -> tutup otomatis
     */
    public function syncDelayFor(Shipment $shipment, ?int $userId = null): ?ShipmentException
    {
        $info    = $this->eta->evaluate($shipment);
        $delayed = $info !== null && $info['delayed'];

        $existing = ShipmentException::where('shipment_id', $shipment->id)
            ->where('description', 'like', self::AUTO_DELAY . '%')
            ->latest('id')
            ->first();

        if (! $delayed) {
            if ($existing && $existing->status !== 'RESOLVED') {
                $old = $existing->status;
                $existing->update(['status' => 'RESOLVED', 'resolved_at' => now()]);
                $this->record($existing, $userId, 'STATUS_CHANGED', $old, 'RESOLVED',
                    '[AUTO-RESOLVED] Shipment sudah tidak terlambat atau sudah terkirim.');
            }

            return $existing?->fresh();
        }

        $text = self::AUTO_DELAY . ' ' . $info['reason'];

        if (! $existing) {
            $created = ShipmentException::create([
                'shipment_id' => $shipment->id,
                'type'        => $this->delayType(),
                'location'    => Str::limit('Rute ' . ($shipment->origin ?? '-') . ' → ' . ($shipment->destination ?? '-'), 250, ''),
                'occurred_at' => now(),
                'severity'    => $info['severity'],
                'status'      => 'OPEN',
                'description' => $text,
                'cause'       => 'Estimasi kedatangan (ETA) melewati jadwal kedatangan yang diharapkan.',
                'impact'      => 'Pengiriman terlambat sekitar ' . $info['delay_hours'] . ' jam.',
                'reported_by' => $userId,
            ]);

            $this->record($created, $userId, 'CREATED', null, 'OPEN', '[AUTO] Delay terdeteksi otomatis. ' . $info['reason']);

            return $created;
        }

        if ($existing->status === 'RESOLVED') {
            // Dibuka lagi hanya jika sebelumnya ditutup otomatis oleh sistem.
            if ($this->wasAutoResolved($existing)) {
                $existing->update(['status' => 'OPEN', 'resolved_at' => null, 'severity' => $info['severity'], 'description' => $text]);
                $this->record($existing, $userId, 'STATUS_CHANGED', 'RESOLVED', 'OPEN', '[AUTO] Shipment kembali terlambat. ' . $info['reason']);
            }

            return $existing->fresh();
        }

        if ($existing->severity !== $info['severity'] || $existing->description !== $text) {
            $old = $existing->severity;
            $existing->update(['severity' => $info['severity'], 'description' => $text]);
            $this->record($existing, $userId, 'UPDATED', $existing->status, $existing->status,
                "[AUTO] Perhitungan delay diperbarui (severity {$old} → {$info['severity']}). " . $info['reason']);
        }

        return $existing->fresh();
    }

    private function delayType(): string
    {
        $type = config('cargotrace.delay.exception_type', 'PORT_CONGESTION');

        return in_array($type, self::TYPES, true) ? $type : 'PORT_CONGESTION';
    }

    private function wasAutoResolved(ShipmentException $exception): bool
    {
        $note = ExceptionHistory::where('exception_id', $exception->id)
            ->where('new_status', 'RESOLVED')
            ->latest('id')
            ->value('note');

        return str_starts_with((string) $note, '[AUTO-RESOLVED]');
    }

    private function record(ShipmentException $exception, ?int $userId, string $action, ?string $old, ?string $new, ?string $note): void
    {
        ExceptionHistory::create([
            'exception_id' => $exception->id,
            'user_id'      => $userId,
            'action'       => $action,
            'old_status'   => $old,
            'new_status'   => $new,
            'note'         => $note,
        ]);
    }
}
