<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Jadwal kedatangan yang dijanjikan. estimated_arrival = ETA terkini.
            $table->dateTime('expected_arrival')->nullable()->after('estimated_arrival');
        });

        // Data lama: anggap janji awal = ETA saat ini (belum terlambat).
        DB::table('shipments')
            ->whereNull('expected_arrival')
            ->update(['expected_arrival' => DB::raw('estimated_arrival')]);
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('expected_arrival');
        });
    }
};
