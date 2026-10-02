<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_siswa'     => ['required', 'string', 'max:255'],
            'kelas'          => ['nullable', 'string', 'max:50'],
            'kompi'          => ['nullable', 'string', 'max:50'],
            'nama_pengirim'  => ['nullable', 'string', 'max:255'],
            'ekspedisi'      => ['nullable', 'string', 'max:100'],
            'nomor_resi'     => ['nullable', 'string', 'max:100'],
            // Saat edit, foto opsional — hanya divalidasi jika petugas mengunggah foto baru
            'foto_paket'     => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'tanggal_datang' => ['nullable', 'date'],
            'keterangan'     => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'required'          => ':attribute wajib diisi.',
            'foto_paket.image'  => 'File yang diunggah harus berupa gambar.',
            'foto_paket.mimes'  => 'Format foto hanya boleh JPG, JPEG, atau PNG.',
            'foto_paket.max'    => 'Ukuran foto maksimal 5 MB.',
            'tanggal_datang.date' => 'Format tanggal tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_siswa'     => 'Nama siswa',
            'kelas'          => 'Kelas',
            'kompi'          => 'Graha',
            'nama_pengirim'  => 'Nama pengirim',
            'ekspedisi'      => 'Ekspedisi',
            'nomor_resi'     => 'Nomor resi',
            'foto_paket'     => 'Foto paket',
            'tanggal_datang' => 'Tanggal paket datang',
            'keterangan'     => 'Keterangan',
        ];
    }
}
