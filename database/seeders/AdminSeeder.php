<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@cargotrace.com'],
            [
                'name' => 'Admin CargoTrace',
                'password' => Hash::make('admin12345'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
