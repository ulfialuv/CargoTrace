<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Identitas pengguna yang melakukan aktivitas
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_role', 30)->nullable();

            // Informasi aktivitas
            $table->string('action', 50);
            $table->string('module', 100);
            $table->string('description', 500)->nullable();

            // Data yang terpengaruh
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('reference_number')->nullable();

            // Riwayat sebelum dan sesudah perubahan
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Informasi tambahan
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index('action');
            $table->index('module');
            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
