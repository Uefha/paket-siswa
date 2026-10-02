<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migration.
     * Membuat tabel `pakets` untuk menyimpan seluruh data paket siswa.
     */
    public function up(): void
    {
        Schema::create('pakets', function (Blueprint $table) {
            $table->id();

            // Data identitas siswa penerima paket
            $table->string('nama_siswa');
            $table->string('kelas');
            $table->string('kompi');

            // Data pengiriman
            $table->string('nama_pengirim');
            $table->string('ekspedisi');
            $table->string('nomor_resi');
            $table->string('foto_paket'); // path relatif di storage/app/public/paket

            // Tanggal & status pengambilan
            $table->date('tanggal_datang');
            $table->date('tanggal_diambil')->nullable();
            $table->enum('status', ['Belum Diambil', 'Sudah Diambil'])
                  ->default('Belum Diambil');

            // Siapa petugas yang memproses pengambilan (opsional, multi-user)
            $table->foreignId('diambil_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Index untuk mempercepat pencarian & filter yang sering dipakai
            $table->index('status');
            $table->index('kelas');
            $table->index('kompi');
            $table->index('ekspedisi');
            $table->index('tanggal_datang');
        });
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('pakets');
    }
};
