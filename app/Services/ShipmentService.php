<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Siklus hidup data shipment: buat, ubah, hapus, plus aturan bisnisnya. */
class ShipmentService
{
    public function __construct(
        private TrackingService $tracking,
        private ExceptionService $exceptions,
    ) {
    }

    public function create(array $data, User $actor): Shipment
    {
        $data['status']          = 'CREATED'; // shipment baru selalu mulai dari CREATED
        $data['shipment_number'] = $data['shipment_number'] ?? $this->nextNumber();

        return DB::transaction(function () use ($data, $actor) {
            $shipment = Shipment::create($data);

            $this->tracking->recordTracking($shipment, 'CREATED', [
                'location'    => $shipment->origin,
                'description' => 'Pengiriman dibuat.',
            ], $actor->name);

            $this->exceptions->syncDelayFor($shipment, $actor->id);

            return $shipment;
        });
    }

    public function update(Shipment $shipment, array $data, User $actor): Shipment
    {
        if ($shipment->status === 'DELIVERED' && $actor->role !== 'admin') {
            throw new BusinessRuleException('Pengiriman DELIVERED bersifat terminal. Hanya admin yang dapat mengubahnya.', 422);
        }

        unset($data['shipment_number'], $data['status']); // nomor & status tidak diubah lewat update biasa

        return DB::transaction(function () use ($shipment, $data, $actor) {
            $shipment->update($data);

            // ETA / expected bisa berubah -> hitung ulang delay.
            $this->exceptions->syncDelayFor($shipment->fresh(), $actor->id);

            return $shipment->fresh();
        });
    }

    public function delete(Shipment $shipment): void
    {
        if (Schema::hasTable('shipment_payments')
            && DB::table('shipment_payments')->where('shipment_id', $shipment->id)->where('status', 'PAID')->exists()
        ) {
            throw new BusinessRuleException('Pengiriman yang sudah dibayar tidak dapat dihapus.', 409);
        }

        $shipment->delete(); // tracking & exceptions ikut terhapus (cascade)
    }

    public function nextNumber(): string
    {
        $i = (int) Shipment::max('id') + 1;

        do {
            $number = 'CT-' . now()->year . '-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $i++;
        } while (Shipment::where('shipment_number', $number)->exists());

        return $number;
    }
}
