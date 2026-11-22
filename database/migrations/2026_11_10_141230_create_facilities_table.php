<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();

            // Identitas fasilitas
            $table->string('facility_code', 50)->unique();
            $table->string('name');

            // Jenis fasilitas
            $table->enum('type', [
                'PORT',
                'AIRPORT',
                'DEPOT',
                'WAREHOUSE'
            ]);

            // Lokasi fasilitas
            $table->text('address');
            $table->string('city');
            $table->string('province');
            $table->string('country')->default('Indonesia');

            // Koordinat untuk peta
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Kapasitas fasilitas
            $table->decimal('capacity', 12, 2)->nullable();

            // Status fasilitas
            $table->enum('status', [
                'ACTIVE',
                'INACTIVE'
            ])->default('ACTIVE');

            // Deskripsi
            $table->text('description')->nullable();

            // Waktu dibuat dan diperbarui
            $table->timestamps();

            // Index untuk pencarian dan filter
            $table->index(['type', 'status']);
            $table->index(['city', 'province']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
