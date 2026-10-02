@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

    <h4 class="fw-bold mb-3"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>

    @if ($jumlahTerlambat > 0)
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
            <div>Terdapat <b>{{ $jumlahTerlambat }}</b> paket yang belum diambil lebih dari 3 hari.</div>
        </div>
    @endif

    {{-- ==================== KARTU STATISTIK ==================== --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-stat h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="bi bi-box-seam"></i></div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $totalPaket }}</div>
                        <div class="text-muted small">Total Paket</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-stat h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-warning bg-opacity-25 text-warning"><i class="bi bi-clock-fill"></i></div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $belumDiambil }}</div>
                        <div class="text-muted small">Belum Diambil</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-stat h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $sudahDiambil }}</div>
                        <div class="text-muted small">Sudah Diambil</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-stat h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $jumlahTerlambat }}</div>
                        <div class="text-muted small">Melebihi 3 Hari</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-stat h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-info bg-opacity-10 text-info"><i class="bi bi-calendar-event"></i></div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $paketHariIni }}</div>
                        <div class="text-muted small">Datang Hari Ini</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- ==================== GRAFIK BULANAN ==================== --}}
        <div class="col-lg-7">
            <div class="card card-stat h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Jumlah Paket Datang per Bulan</h6>
                    <canvas id="grafikPaketBulanan" height="120"></canvas>
                </div>
            </div>
        </div>

        {{-- ==================== NOTIFIKASI KETERLAMBATAN ==================== --}}
        <div class="col-lg-5">
            <div class="card card-stat h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-bell-fill text-danger me-1"></i> Paket yang Harus Segera Diambil
                    </h6>

                    @if ($notifikasiTerlambat->isEmpty())
                        <div class="empty-state py-4">
                            <i class="bi bi-emoji-smile d-block"></i>
                            <p class="mb-0">Tidak ada paket yang terlambat diambil. Mantap!</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>Lama Menunggu</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notifikasiTerlambat as $paket)
                                        <tr>
                                            <td>
                                                <a href="{{ route('paket.show', $paket) }}" class="text-decoration-none">
                                                    {{ $paket->nama_siswa }}
                                                </a>
                                                <div class="text-muted small">{{ $paket->kelas ?: '-' }} · {{ $paket->kompi ?: '-' }}</div>
                                            </td>
                                            <td>{{ $paket->lama_menunggu }} Hari</td>
                                            <td><span class="badge bg-danger">⚠ Terlambat</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <a href="{{ route('paket.index') }}" class="small">Lihat semua data paket &rarr;</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const ctxGrafik = document.getElementById('grafikPaketBulanan');
        new Chart(ctxGrafik, {
            type: 'bar',
            data: {
                labels: @json($labelGrafik),
                datasets: [{
                    label: 'Jumlah Paket',
                    data: @json($dataGrafik),
                    backgroundColor: '#0d6efd',
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    </script>
@endpush
