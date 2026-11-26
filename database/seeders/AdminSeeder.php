<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Membuat akun admin bila belum ada. Akun yang sudah ada TIDAK diubah
 * (password lama tetap), hanya dilengkapi role bila masih kosong.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrNew(['email' => 'admin@cargotrace.com']);

        if (! $user->exists) {
            $user->fill([
                'name'     => 'Admin CargoTrace',
                'password' => (string) config('cargotrace.demo.password', 'password'),
            ]);
        }

        if (blank($user->role)) {
            $user->role = 'admin';
        }

        $user->save();
    }
}
