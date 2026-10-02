@extends('layouts.app')

@section('title', 'Detail Paket')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('paket.index') }}">Data Paket</a></li>
    <li class="breadcrumb-item active">Detail Paket</li>
@endsection

@section('content')

    @php $badge = $paket->badge_status; @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0"><i class="bi bi-eye me-2"></i>Detail Paket</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('paket.edit', $paket) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('paket.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card card-stat">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    @if ($paket->foto_url)
                        <img src="{{ $paket->foto_url }}" alt="Foto paket {{ $paket->nama_siswa }}"
                             class="img-fluid rounded shadow-sm" style="max-height:320px; object-fit:cover;">
                    @else
                        <div class="empty-state">
                            <i class="bi bi-image d-block"></i>
                            <p class="mb-0">Tidak ada foto</p>
                        </div>
                    @endif
                </div>

                <div class="col-md-8">
                    <span class="badge {{ $badge['class'] }} fs-6 mb-3">
                        <i class="bi {{ $badge['icon'] }} me-1"></i>{{ $badge['label'] }}
                    </span>

                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr><th style="width:220px;">Nama Siswa</th><td>: {{ $paket->nama_siswa }}</td></tr>
                            <tr><th>Kelas</th><td>: {{ $paket->kelas ?: '-' }}</td></tr>
                            <tr><th>Graha</th><td>: {{ $paket->kompi ?: '-' }}</td></tr>
                            <tr><th>Nama Pengirim</th><td>: {{ $paket->nama_pengirim ?: '-' }}</td></tr>
                            <tr><th>Ekspedisi</th><td>: {{ $paket->ekspedisi ?: '-' }}</td></tr>
                            <tr><th>Nomor Resi</th><td>: {{ $paket->nomor_resi ?: '-' }}</td></tr>
                            <tr><th>Tanggal Datang</th><td>: {{ $paket->tanggal_datang ? $paket->tanggal_datang->format('d F Y') : '-' }}</td></tr>
                            <tr><th>Lama Menunggu</th><td>: {{ $paket->lama_menunggu }} hari</td></tr>
                            <tr>
                                <th>Tanggal Diambil</th>
                                <td>: {{ $paket->tanggal_diambil ? $paket->tanggal_diambil->format('d F Y') : '-' }}</td>
                            </tr>
                            @if ($paket->petugasPengambilan)
                                <tr><th>Diproses Oleh</th><td>: {{ $paket->petugasPengambilan->name }}</td></tr>
                            @endif
                            <tr><th>Keterangan</th><td>: {{ $paket->keterangan ?: '-' }}</td></tr>
                        </tbody>
                    </table>

                    @if ($paket->status === \App\Models\Paket::STATUS_BELUM)
                        <button type="button"
                                class="btn btn-success mt-3 btn-tandai-diambil"
                                data-url="{{ route('paket.tandaiDiambil', $paket) }}"
                                data-nama="{{ $paket->nama_siswa }}">
                            <i class="bi bi-check2-circle me-1"></i> Tandai Sudah Diambil
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

{{-- Tombol "Tandai Sudah Diambil" di atas sudah ditangani otomatis oleh public/js/paket-app.js
     (delegasi event pada class .btn-tandai-diambil), termasuk reload halaman ini setelah berhasil. --}}
