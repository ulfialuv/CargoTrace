<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_tracking', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->cascadeOnDelete();

            $table->string('status');
            $table->string('location');
            $table->dateTime('event_time');

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->text('description')->nullable();
            $table->string('updated_by')->nullable();

            $table->timestamps();

            $table->index(['shipment_id', 'event_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_tracking');
    }
};
