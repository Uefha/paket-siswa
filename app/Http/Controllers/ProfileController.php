<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman "Profil Saya" — berisi form ubah info akun
     * (nama & email, di mana email dipakai sebagai username login)
     * dan form ubah password, terpisah.
     */
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Perbarui nama & email milik user yang sedang login.
     * Email harus unik (kecuali milik user itu sendiri).
     */
    public function updateInfo(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ], [
            'required'     => ':attribute wajib diisi.',
            'email.email'  => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah dipakai akun lain.',
        ], [
            'name'  => 'Nama',
            'email' => 'Email',
        ]);

        $user->fill($validated);
        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Informasi akun berhasil diperbarui.');
    }

    /**
     * Perbarui password login user yang sedang aktif.
     * Wajib mengonfirmasi password lama terlebih dahulu (rule 'current_password').
     * Password baru otomatis ter-hash lewat cast 'hashed' pada model User.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'          => ['required', 'confirmed', Password::min(8)],
        ], [
            'required'                           => ':attribute wajib diisi.',
            'current_password.current_password'  => 'Password saat ini tidak sesuai.',
            'password.confirmed'                 => 'Konfirmasi password baru tidak cocok.',
            'password.min'                        => 'Password baru minimal 8 karakter.',
        ], [
            'current_password' => 'Password saat ini',
            'password'          => 'Password baru',
        ]);

        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Password berhasil diperbarui. Gunakan password baru saat login berikutnya.');
    }
}
