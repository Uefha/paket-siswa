<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Sistem Pendataan Paket Siswa SMA Taruna Nusantara
|--------------------------------------------------------------------------
| Seluruh route di bawah ini (kecuali auth) hanya bisa diakses oleh
| petugas/admin yang sudah login (middleware 'auth' dari Laravel Breeze).
*/

Route::redirect('/', '/dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil Saya — ubah nama/email (username login) & ubah password
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'updateInfo'])->name('profile.updateInfo');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    // Endpoint JSON untuk DataTables server-side — didaftarkan sebelum resource
    // route agar tidak bentrok dengan wildcard {paket} pada route show/edit.
    Route::get('/paket/data', [PaketController::class, 'data'])->name('paket.data');

    // Export & cetak laporan
    Route::get('/paket/export/excel', [PaketController::class, 'exportExcel'])->name('paket.export.excel');
    Route::get('/paket/export/pdf', [PaketController::class, 'exportPdf'])->name('paket.export.pdf');

    // Ubah status paket menjadi "Sudah Diambil"
    Route::patch('/paket/{paket}/tandai-diambil', [PaketController::class, 'tandaiDiambil'])->name('paket.tandaiDiambil');

    // CRUD utama data paket
    Route::resource('paket', PaketController::class)->except(['data']);
});

require __DIR__ . '/auth.php';
