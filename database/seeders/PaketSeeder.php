<?php

namespace Database\Seeders;

use App\Models\Paket;
use Illuminate\Database\Seeder;

class PaketSeeder extends Seeder
{
    /**
     * Mengisi 40 data paket contoh agar dashboard & tabel langsung terlihat
     * berisi saat aplikasi pertama kali dijalankan (demo/testing).
     */
    public function run(): void
    {
        Paket::factory()->count(40)->create();
    }
}
