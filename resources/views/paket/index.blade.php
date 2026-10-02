@extends('layouts.app')

@section('title', 'Data Paket')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Data Paket</li>
@endsection

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>Data Paket Siswa</h4>
        <a href="{{ route('paket.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Paket
        </a>
    </div>

    <div class="card card-stat mb-3">
        <div class="card-body">
            {{-- ==================== BARIS PENCARIAN & EXPORT ==================== --}}
            <div class="row g-2 align-items-center mb-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="pencarianPaket" class="form-control"
                               placeholder="Cari nama siswa, no. resi, pengirim, kelas, kompi, ekspedisi...">
                    </div>
                </div>
                <div class="col-md-7 text-md-end">
                    <button class="btn btn-outline-success" id="btnExportExcel" data-url="{{ route('paket.export.excel') }}">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                    </button>
                    <button class="btn btn-outline-danger" id="btnExportPdf" data-url="{{ route('paket.export.pdf') }}">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                    </button>
                </div>
            </div>

            {{-- ==================== BARIS FILTER ==================== --}}
            <div class="row g-2 mb-3">
                <div class="col-6 col-md-2">
                    <select id="filterStatus" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="{{ \App\Models\Paket::STATUS_BELUM }}">Belum Diambil</option>
                        <option value="{{ \App\Models\Paket::STATUS_SUDAH }}">Sudah Diambil</option>
                        <option value="terlambat">Terlambat (&gt;3 Hari)</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select id="filterKelas" class="form-select form-select-sm">
                        <option value="">Semua Kelas</option>
                        @foreach ($daftarKelas as $kelas)
                            <option value="{{ $kelas }}">{{ $kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select id="filterKompi" class="form-select form-select-sm">
                        <option value="">Semua Graha</option>
                        @foreach ($daftarKompi as $kompi)
                            <option value="{{ $kompi }}">{{ $kompi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select id="filterEkspedisi" class="form-select form-select-sm">
                        <option value="">Semua Ekspedisi</option>
                        @foreach ($daftarEkspedisi as $ekspedisi)
                            <option value="{{ $ekspedisi }}">{{ $ekspedisi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select id="filterBulan" class="form-select form-select-sm">
                        <option value="">Semua Bulan</option>
                        @foreach (['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $angka => $nama)
                            <option value="{{ $angka }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select id="filterTahun" class="form-select form-select-sm">
                        <option value="">Semua Tahun</option>
                        @for ($tahun = now()->year; $tahun >= now()->year - 3; $tahun--)
                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- Rentang tanggal — dipakai untuk mempersempit tabel sekaligus dasar
                 "Cetak laporan berdasarkan rentang tanggal" (Export PDF/Excel) --}}
            <div class="row g-2 mb-3 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted mb-1">Dari Tanggal</label>
                    <input type="date" id="filterDari" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
                    <input type="date" id="filterSampai" class="form-control form-control-sm">
                </div>
                <div class="col-12 col-md-6">
                    <button type="button" id="btnResetTanggal" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Reset Rentang Tanggal
                    </button>
                </div>
            </div>

            {{-- ==================== TABEL DATA ==================== --}}
            <div class="table-responsive">
                <table id="tabelPaket" data-url="{{ route('paket.data') }}" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Foto</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Graha</th>
                            <th>Pengirim</th>
                            <th>Ekspedisi</th>
                            <th>No. Resi</th>
                            <th>Tgl Datang</th>
                            <th>Lama Menunggu</th>
                            <th>Status</th>
                            <th>Tgl Diambil</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                {{-- Empty state kustom, disembunyikan/ditampilkan lewat JS (paket-app.js) --}}
                <div id="emptyStatePaket" class="empty-state d-none">
                    <i class="bi bi-inbox d-block"></i>
                    <p class="mb-0">Belum ada data paket yang cocok dengan pencarian/filter ini.</p>
                </div>
            </div>
        </div>
    </div>

@endsection
