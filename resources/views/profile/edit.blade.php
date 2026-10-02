@extends('layouts.app')

@section('title', 'Profil Saya')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Profil Saya</li>
@endsection

@section('content')

    <h4 class="fw-bold mb-3"><i class="bi bi-person-circle me-2"></i>Profil Saya</h4>

    <div class="row g-3">
        {{-- ==================== FORM INFORMASI AKUN (NAMA & EMAIL/USERNAME) ==================== --}}
        <div class="col-lg-6">
            <div class="card card-stat h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-1">Informasi Akun</h6>
                    <p class="text-muted small mb-3">Email di bawah ini adalah username yang dipakai untuk login.</p>

                    <form action="{{ route('profile.updateInfo') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email (Username Login)</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================== FORM UBAH PASSWORD ==================== --}}
        <div class="col-lg-6">
            <div class="card card-stat h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-1">Ubah Password</h6>
                    <p class="text-muted small mb-3">Masukkan password saat ini untuk konfirmasi, lalu password baru Anda.</p>

                    <form action="{{ route('profile.updatePassword') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Password Saat Ini</label>
                            <input type="password" name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Minimal 8 karakter.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-shield-lock me-1"></i> Ubah Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
