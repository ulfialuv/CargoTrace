<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentTracking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/**
 * Tracking event + aturan transisi status shipment:
 *   CREATED -> BOOKED -> PICKED_UP -> IN_TRANSIT -> DELIVERED (DELIVERED terminal).
 */
class TrackingService
{
    public const STATUSES = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];

    /** Barang baru boleh dijemput kalau pembayaran LUNAS (null = matikan aturan). */
    public const PAYMENT_GATE = 'PICKED_UP';

    public function __construct(private ExceptionService $exceptions)
    {
    }

    public function nextOf(string $from): ?string
    {
        $i = array_search($from, self::STATUSES, true);

        return ($i === false || ! isset(self::STATUSES[$i + 1])) ? null : self::STATUSES[$i + 1];
    }

    /**
     * Pindahkan status ke langkah berikutnya + catat tracking event.
     * Melempar ValidationException (422) jika transisi tidak valid.
     */
    public function transition(Shipment $shipment, string $to, array $event, string $actor, ?int $actorId = null): ShipmentTracking
    {
        return DB::transaction(function () use ($shipment, $to, $event, $actor, $actorId) {
            $locked = Shipment::whereKey($shipment->id)->lockForUpdate()->firstOrFail();
            $from   = (string) $locked->status;

            if ($from === 'DELIVERED') {
                $this->reject('Status DELIVERED bersifat terminal dan tidak dapat diubah.');
            }

            $next = $this->nextOf($from);

            if ($to !== $next) {
                $this->reject("Transisi tidak valid: {$from} → {$to}. Status berikutnya yang diizinkan hanya {$next}.");
            }

            if (self::PAYMENT_GATE !== null && $to === self::PAYMENT_GATE && ! $this->isPaid($locked->id)) {
                $this->reject("Pembayaran belum lunas. Pengiriman belum dapat berstatus {$to}.");
            }

            $locked->status = $to;
            $locked->save();
            $shipment->status = $to;

            $tracking = $this->recordTracking($locked, $to, $event, $actor);

            if ($to === 'DELIVERED') {
                // Sudah terkirim: exception delay otomatis ikut ditutup.
                $this->exceptions->syncDelayFor($locked, $actorId);
            }

            return $tracking;
        });
    }

    /** Catat event baru tanpa mengubah status (mis. update posisi saat IN_TRANSIT). */
    public function recordEvent(Shipment $shipment, array $event, string $actor): ShipmentTracking
    {
        if ($shipment->status === 'DELIVERED') {
            $this->reject('Pengiriman sudah DELIVERED, tidak bisa menambah event baru.');
        }

        return $this->recordTracking($shipment, (string) $shipment->status, $event, $actor);
    }

    public function recordTracking(Shipment $shipment, string $status, array $event, string $actor): ShipmentTracking
    {
        $location = trim((string) ($event['location'] ?? ''));

        if ($location === '') {
            $last = $shipment->trackingEvents()->orderByDesc('event_time')->first();

            $location = $status === 'DELIVERED'
                ? $shipment->destination
                : ($last->location ?? $shipment->origin);
        }

        return ShipmentTracking::create([
            'shipment_id' => $shipment->id,
            'status'      => $status,
            'location'    => $location,
            'event_time'  => $event['event_time'] ?? now(),
            'latitude'    => $event['latitude'] ?? null,
            'longitude'   => $event['longitude'] ?? null,
            'description' => $event['description'] ?? null,
            'updated_by'  => $actor,
        ]);
    }

    public function isPaid(int $shipmentId): bool
    {
        if (! Schema::hasTable('shipment_payments')) {
            return true; // modul pembayaran belum aktif -> gerbang tidak berlaku
        }

        return DB::table('shipment_payments')
            ->where('shipment_id', $shipmentId)
            ->where('status', 'PAID')
            ->exists();
    }

    private function reject(string $message): never
    {
        throw ValidationException::withMessages(['status' => [$message]]);
    }
}
