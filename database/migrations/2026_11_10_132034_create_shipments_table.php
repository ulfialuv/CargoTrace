<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {

            $table->id();

            // Informasi Shipment
            $table->string('shipment_number')->unique();
            $table->string('customer_name');
            $table->string('shipment_name');
            $table->string('cargo_type');
            $table->decimal('weight', 10, 2);
            $table->decimal('volume', 10, 2)->nullable();

            // Informasi Transportasi
            $table->string('carrier');
            $table->string('transport_type');
            $table->string('origin');
            $table->string('destination');

            // Jadwal Pengiriman
            $table->dateTime('estimated_departure');
            $table->dateTime('estimated_arrival');

            // Status Shipment
            $table->enum('status', [
                'CREATED',
                'BOOKED',
                'PICKED_UP',
                'IN_TRANSIT',
                'DELIVERED'
            ])->default('CREATED');

            // Informasi Tambahan
            $table->text('notes')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
