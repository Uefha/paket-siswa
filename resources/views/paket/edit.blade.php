@extends('layouts.app')

@section('title', 'Edit Paket')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('paket.index') }}">Data Paket</a></li>
    <li class="breadcrumb-item active">Edit Paket</li>
@endsection

@section('content')

    <h4 class="fw-bold mb-3"><i class="bi bi-pencil me-2"></i>Edit Data Paket</h4>

    <div class="card card-stat">
        <div class="card-body">
            <form action="{{ route('paket.update', $paket) }}" method="POST" enctype="multipart/form-data" class="form-paket">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                        <input type="text" name="nama_siswa" value="{{ old('nama_siswa', $paket->nama_siswa) }}"
                               class="form-control @error('nama_siswa') is-invalid @enderror" required>
                        @error('nama_siswa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Kelas</label>
                        <input type="text" name="kelas" value="{{ old('kelas', $paket->kelas) }}"
                               class="form-control @error('kelas') is-invalid @enderror">
                        @error('kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Graha</label>
                        <input type="text" name="kompi" value="{{ old('kompi', $paket->kompi) }}"
                               class="form-control @error('kompi') is-invalid @enderror">
                        @error('kompi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nama Pengirim</label>
                        <input type="text" name="nama_pengirim" value="{{ old('nama_pengirim', $paket->nama_pengirim) }}"
                               class="form-control @error('nama_pengirim') is-invalid @enderror">
                        @error('nama_pengirim') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Ekspedisi</label>
                        <input type="text" name="ekspedisi" value="{{ old('ekspedisi', $paket->ekspedisi) }}"
                               class="form-control @error('ekspedisi') is-invalid @enderror">
                        @error('ekspedisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nomor Resi</label>
                        <input type="text" name="nomor_resi" value="{{ old('nomor_resi', $paket->nomor_resi) }}"
                               class="form-control @error('nomor_resi') is-invalid @enderror">
                        @error('nomor_resi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tanggal Paket Datang</label>
                        <input type="date" name="tanggal_datang"
                               value="{{ old('tanggal_datang', $paket->tanggal_datang?->format('Y-m-d')) }}"
                               class="form-control @error('tanggal_datang') is-invalid @enderror">
                        @error('tanggal_datang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" rows="1" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $paket->keterangan) }}</textarea>
                        @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Ganti Foto Paket</label>
                        <div class="d-flex gap-2">
                            <input type="file" name="foto_paket" id="inputFotoPaket" accept=".jpg,.jpeg,.png"
                                   class="form-control @error('foto_paket') is-invalid @enderror">
                            <button type="button" class="btn btn-outline-primary flex-shrink-0" title="Ambil dari kamera"
                                    data-bs-toggle="modal" data-bs-target="#modalKamera">
                                <i class="bi bi-camera-fill"></i>
                            </button>
                        </div>
                        <div class="form-text">Kosongkan bila tidak ingin mengganti foto. Bisa pilih file atau ambil langsung dari kamera. Maksimal 5 MB.</div>
                        @error('foto_paket') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Foto Saat Ini</label>
                        <div>
                            <img src="{{ $paket->foto_url }}" alt="Foto paket saat ini"
                                 style="max-width:220px;max-height:220px;object-fit:cover;border-radius:.5rem;">
                            <img id="previewFoto" src="#" alt="Pratinjau foto baru" class="mt-2">
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('paket.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    @include('paket._kamera-modal')

@endsection
