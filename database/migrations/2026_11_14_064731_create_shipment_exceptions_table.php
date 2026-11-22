<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipment_exceptions', function (Blueprint $table) {
            $table->id();

            // Relasi dengan data pengiriman
            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->cascadeOnDelete();

            // Informasi pengecualian
            $table->string('type');
            $table->string('location');
            $table->dateTime('occurred_at');

            // Tingkat keparahan
            $table->enum('severity', [
                'LOW',
                'MEDIUM',
                'HIGH'
            ])->default('LOW');

            // Status penanganan
            $table->enum('status', [
                'OPEN',
                'INVESTIGATING',
                'RESOLVED'
            ])->default('OPEN');

            // Detail pengecualian
            $table->text('description');
            $table->text('cause')->nullable();
            $table->text('impact')->nullable();

            // Petugas yang menangani
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Pengguna yang melaporkan
            $table->foreignId('reported_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Informasi penyelesaian
            $table->dateTime('resolved_at')->nullable();

            // Lampiran bukti
            $table->string('attachment_path')->nullable();

            $table->timestamps();

            // Index untuk pencarian dan filter
            $table->index(['status', 'severity']);
            $table->index(['type', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_exceptions');
    }
};
