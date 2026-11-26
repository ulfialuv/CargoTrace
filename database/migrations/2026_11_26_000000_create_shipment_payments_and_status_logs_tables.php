<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Skema ini sebelumnya dibuat "diam-diam" oleh PengirimanController::bootstrapPayments().
 * Dipindah ke migration supaya terdokumentasi, bisa di-rollback, dan ikut dibuat saat
 * `php artisan migrate:fresh --seed` serta saat test.
 *
 * Aman dijalankan di database yang tabelnya sudah terlanjur dibuat (ada pengecekan hasTable).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('shipment_payments')) {
            Schema::create('shipment_payments', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('shipment_id')->index();
                $t->string('invoice_number', 40)->unique();
                $t->string('method', 20);
                $t->string('payment_code', 120)->nullable();
                $t->unsignedBigInteger('subtotal');
                $t->unsignedBigInteger('tax');
                $t->unsignedBigInteger('fee');
                $t->unsignedBigInteger('total');
                $t->string('currency', 3)->default('IDR');
                $t->string('status', 12)->index();
                $t->timestamp('expires_at')->nullable();
                $t->timestamp('paid_at')->nullable();
                $t->timestamp('refunded_at')->nullable();
                $t->string('gateway_ref', 40)->nullable();
                $t->string('note')->nullable();
                $t->string('created_by')->nullable();
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('shipment_status_logs')) {
            Schema::create('shipment_status_logs', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('shipment_id')->index();
                $t->string('from_status', 20)->nullable();
                $t->string('to_status', 20);
                $t->string('type', 12)->default('TRANSITION');
                $t->text('reason')->nullable();
                $t->string('actor')->nullable();
                $t->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_status_logs');
        Schema::dropIfExists('shipment_payments');
    }
};
