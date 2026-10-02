<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Revisi: hanya "Nama Siswa" dan "Foto Paket" yang wajib diisi.
     * Kolom lain (kelas, kompi/graha, pengirim, ekspedisi, no. resi, tanggal datang)
     * dijadikan boleh kosong di level database, mengikuti pelonggaran validasi
     * di StorePaketRequest & UpdatePaketRequest.
     */
    public function up(): void
    {
        Schema::table('pakets', function (Blueprint $table) {
            $table->string('kelas')->nullable()->change();
            $table->string('kompi')->nullable()->change();
            $table->string('nama_pengirim')->nullable()->change();
            $table->string('ekspedisi')->nullable()->change();
            $table->string('nomor_resi')->nullable()->change();
            $table->date('tanggal_datang')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pakets', function (Blueprint $table) {
            $table->string('kelas')->nullable(false)->change();
            $table->string('kompi')->nullable(false)->change();
            $table->string('nama_pengirim')->nullable(false)->change();
            $table->string('ekspedisi')->nullable(false)->change();
            $table->string('nomor_resi')->nullable(false)->change();
            $table->date('tanggal_datang')->nullable(false)->change();
        });
    }
};
