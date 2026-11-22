<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Informasi pribadi
            $table->string('phone', 30)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('profile_photo_path')->nullable();

            // Informasi kontak dan alamat
            $table->string('whatsapp_number', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->nullable();

            // Preferensi notifikasi
            $table->boolean('notify_shipment_updates')->default(true);
            $table->boolean('notify_tracking_updates')->default(true);
            $table->boolean('notify_delay_alerts')->default(true);
            $table->boolean('notify_exception_alerts')->default(true);
            $table->boolean('notify_email')->default(false);

            // Aktivitas akun
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('profile_updated_at')->nullable();
            $table->timestamp('password_changed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'birth_date',
                'gender',
                'profile_photo_path',
                'whatsapp_number',
                'address',
                'city',
                'province',
                'postal_code',
                'country',
                'notify_shipment_updates',
                'notify_tracking_updates',
                'notify_delay_alerts',
                'notify_exception_alerts',
                'notify_email',
                'last_login_at',
                'profile_updated_at',
                'password_changed_at',
            ]);
        });
    }
};
