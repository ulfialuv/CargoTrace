<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Isi database dengan data demo: akun, carrier, lokasi, shipment,
     * tracking event, dan exception. Aman dijalankan berulang kali.
     */
    public function run(): void
    {
        $this->call(DemoSeeder::class);
    }
}
