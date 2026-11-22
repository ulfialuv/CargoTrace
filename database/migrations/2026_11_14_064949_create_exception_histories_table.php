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
        Schema::create('exception_histories', function (Blueprint $table) {
            $table->id();

            // Relasi dengan data pengecualian
            $table->foreignId('exception_id')
                ->constrained('shipment_exceptions')
                ->cascadeOnDelete();

            // Petugas yang melakukan aktivitas
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Jenis aktivitas
            $table->string('action');

            // Riwayat perubahan status
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();

            // Catatan aktivitas petugas
            $table->text('note')->nullable();

            $table->timestamps();

            // Index pencarian riwayat
            $table->index(['exception_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exception_histories');
    }
};
