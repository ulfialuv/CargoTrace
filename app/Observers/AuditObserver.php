<?php

namespace App\Observers;

use App\Models\Carrier;
use App\Models\Facility;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Mencatat tambah / ubah / hapus pada data utama ke audit log,
 * baik dari halaman web, API, maupun service.
 */
class AuditObserver
{
    /** Kolom yang tidak dianggap sebagai perubahan data. */
    private const SKIP_KEYS = ['created_at', 'updated_at', 'remember_token', 'last_login_at'];

    public function __construct(private AuditLogService $audit)
    {
    }

    public function created(Model $model): void
    {
        [$module, $noun, $display, $reference] = $this->meta($model);

        $this->audit->record(
            $this->action($model, 'CREATE'),
            $module,
            "Menambah {$noun} {$display}",
            $model,
            null,
            $this->clean($model->getAttributes()),
            $reference,
        );
    }

    public function updated(Model $model): void
    {
        $changes = Arr::except($model->getChanges(), self::SKIP_KEYS);

        if (! $changes) {
            return; // hanya timestamp / last_login_at yang berubah
        }

        $old = Arr::only($model->getOriginal(), array_keys($changes));
        [$module, $noun, $display, $reference] = $this->meta($model);

        if ($model instanceof Shipment && array_key_exists('status', $changes)) {
            $this->audit->record(
                'STATUS CHANGE',
                $module,
                "Mengubah status {$noun} {$display}: " . ($old['status'] ?? '-') . ' → ' . $changes['status'],
                $model,
                $this->clean($old),
                $this->clean($changes),
                $reference,
            );

            return;
        }

        $this->audit->record(
            $this->action($model, 'UPDATE'),
            $module,
            "Mengubah {$noun} {$display}",
            $model,
            $this->clean($old),
            $this->clean($changes),
            $reference,
        );
    }

    public function deleted(Model $model): void
    {
        [$module, $noun, $display, $reference] = $this->meta($model);

        $this->audit->record(
            $this->action($model, 'DELETE'),
            $module,
            "Menghapus {$noun} {$display}",
            $model,
            $this->clean($model->getAttributes()),
            null,
            $reference,
        );
    }

    /** @return array{0:string,1:string,2:string,3:?string} modul, kata benda, tampilan, nomor referensi */
    private function meta(Model $model): array
    {
        return match (true) {
            $model instanceof Shipment          => ['Pengiriman', 'pengiriman', (string) $model->shipment_number, $model->shipment_number],
            $model instanceof ShipmentException => ['Pengecualian', 'pengecualian', $model->type . ' #' . $model->getKey(), $model->shipment?->shipment_number],
            $model instanceof Carrier           => ['Perusahaan Pengiriman', 'perusahaan pengiriman', (string) $model->name, $model->code],
            $model instanceof Facility          => ['Lokasi & Fasilitas', 'lokasi', (string) $model->name, $model->facility_code],
            $model instanceof User              => ['Pengguna & Hak Akses', 'pengguna', $model->name . ' (' . $model->role . ')', null],
            default                             => [class_basename($model), strtolower(class_basename($model)), '#' . $model->getKey(), null],
        };
    }

    private function action(Model $model, string $verb): string
    {
        return match (true) {
            $model instanceof User                                   => 'USER MANAGEMENT',
            $model instanceof ShipmentException && $verb === 'UPDATE' => 'EXCEPTION UPDATE',
            default                                                   => $verb,
        };
    }

    /** Buang timestamp dan sembunyikan password (nilainya tidak pernah disimpan). */
    private function clean(array $values): array
    {
        $values = Arr::except($values, self::SKIP_KEYS);

        if (array_key_exists('password', $values)) {
            $values['password'] = '***';
        }

        return $values;
    }
}
