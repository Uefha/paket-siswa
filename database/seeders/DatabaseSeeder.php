<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin/petugas default untuk login pertama kali.
        // PENTING: ganti kata sandi ini setelah login pertama di lingkungan produksi.
        User::firstOrCreate(
            ['email' => 'admin@tarunanusantara-ikn.sch.id'],
            [
                'name'     => 'Admin Infolahta',
                'password' => bcrypt('password'),
            ]
        );

        $this->call([
            PaketSeeder::class,
        ]);
    }
}
