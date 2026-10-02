@extends('layouts.app')

@section('title', 'Tambah Paket')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('paket.index') }}">Data Paket</a></li>
    <li class="breadcrumb-item active">Tambah Paket</li>
@endsection

@section('content')

    <h4 class="fw-bold mb-3"><i class="bi bi-plus-circle me-2"></i>Tambah Paket Baru</h4>

    <div class="card card-stat">
        <div class="card-body">
            <form action="{{ route('paket.store') }}" method="POST" enctype="multipart/form-data" class="form-paket">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                        <input type="text" name="nama_siswa" value="{{ old('nama_siswa') }}"
                               class="form-control @error('nama_siswa') is-invalid @enderror" required>
                        @error('nama_siswa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Kelas</label>
                        <input type="text" name="kelas" value="{{ old('kelas') }}" placeholder="Contoh: X-1"
                               class="form-control @error('kelas') is-invalid @enderror">
                        @error('kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Graha</label>
                        <input type="text" name="kompi" value="{{ old('kompi') }}" placeholder="Contoh: Graha A"
                               class="form-control @error('kompi') is-invalid @enderror">
                        @error('kompi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nama Pengirim</label>
                        <input type="text" name="nama_pengirim" value="{{ old('nama_pengirim') }}"
                               class="form-control @error('nama_pengirim') is-invalid @enderror">
                        @error('nama_pengirim') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Ekspedisi</label>
                        <input type="text" name="ekspedisi" value="{{ old('ekspedisi') }}" placeholder="JNE, J&T, SiCepat, dll."
                               class="form-control @error('ekspedisi') is-invalid @enderror">
                        @error('ekspedisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nomor Resi</label>
                        <input type="text" name="nomor_resi" value="{{ old('nomor_resi') }}"
                               class="form-control @error('nomor_resi') is-invalid @enderror">
                        @error('nomor_resi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tanggal Paket Datang</label>
                        <input type="date" name="tanggal_datang" value="{{ old('tanggal_datang', now()->format('Y-m-d')) }}"
                               class="form-control @error('tanggal_datang') is-invalid @enderror">
                        @error('tanggal_datang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" rows="1" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan') }}</textarea>
                        @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Foto Paket <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2">
                            <input type="file" name="foto_paket" id="inputFotoPaket" accept=".jpg,.jpeg,.png"
                                   class="form-control @error('foto_paket') is-invalid @enderror" required>
                            <button type="button" class="btn btn-outline-primary flex-shrink-0" title="Ambil dari kamera"
                                    data-bs-toggle="modal" data-bs-target="#modalKamera">
                                <i class="bi bi-camera-fill"></i>
                            </button>
                        </div>
                        <div class="form-text">Format JPG/JPEG/PNG, maksimal 5 MB. Bisa pilih file atau ambil langsung dari kamera.</div>
                        @error('foto_paket') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Pratinjau Foto</label>
                        <div>
                            <img id="previewFoto" src="#" alt="Pratinjau foto paket">
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Paket
                    </button>
                    <a href="{{ route('paket.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    @include('paket._kamera-modal')

@endsection
