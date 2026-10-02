<?php

namespace Database\Factories;

use App\Models\Paket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PaketFactory extends Factory
{
    protected $model = Paket::class;

    public function definition(): array
    {
        $ekspedisiList = ['JNE', 'J&T Express', 'SiCepat', 'AnterAja', 'Ninja Xpress', 'Pos Indonesia'];
        $kompiList     = ['Kompi A', 'Kompi B', 'Kompi C', 'Kompi D'];
        $kelasList     = ['X-1', 'X-2', 'XI-1', 'XI-2', 'XII-1', 'XII-2'];

        // Tanggal datang acak dalam 10 hari terakhir, agar sebagian data otomatis
        // masuk kategori "terlambat" (>= 3 hari) untuk keperluan demo dashboard.
        $tanggalDatang = Carbon::today()->subDays(fake()->numberBetween(0, 10));

        // ~60% data berstatus sudah diambil
        $sudahDiambil = fake()->boolean(60);

        return [
            'nama_siswa'     => fake()->name(),
            'kelas'          => fake()->randomElement($kelasList),
            'kompi'          => fake()->randomElement($kompiList),
            'nama_pengirim'  => fake()->name(),
            'ekspedisi'      => fake()->randomElement($ekspedisiList),
            'nomor_resi'     => strtoupper(fake()->bothify('??########')),
            // Placeholder path — pada data demo, file fisik tidak otomatis dibuat.
            // Ganti dengan foto asli lewat form edit bila diperlukan.
            'foto_paket'     => 'paket/placeholder.png',
            'tanggal_datang' => $tanggalDatang,
            'tanggal_diambil' => $sudahDiambil ? $tanggalDatang->copy()->addDays(fake()->numberBetween(0, 2)) : null,
            'status'         => $sudahDiambil ? Paket::STATUS_SUDAH : Paket::STATUS_BELUM,
            'keterangan'     => fake()->optional(0.3)->sentence(),
        ];
    }
}
