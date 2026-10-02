<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Sistem Paket Siswa TN IKN</title>

    <!-- Bootstrap 5 & Ikon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- DataTables + Buttons + Responsive extension (Responsive dipakai agar kolom Aksi tetap terlihat di HP) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.6/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.8/css/responsive.bootstrap5.min.css">
    <!-- SweetAlert2 (konfirmasi hapus & toast) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">
    @stack('styles')
</head>
<body>

<div class="d-flex" id="wrapper">

    {{-- ============ SIDEBAR ============ --}}
    <nav class="sidebar bg-dark text-white" id="sidebar">
        <div class="sidebar-brand d-flex align-items-center justify-content-between px-3 py-3 border-bottom border-secondary">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-box-seam-fill fs-4"></i>
                <div>
                    <div class="fw-bold small lh-1">Paket Siswa</div>
                    <div class="text-white-50" style="font-size:.7rem;">SMA Taruna Nusantara IKN</div>
                </div>
            </div>
            <button class="btn btn-sm btn-dark text-white-50 ms-auto" id="btnCloseSidebar" title="Tutup sidebar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <ul class="nav flex-column px-2 py-3">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('paket.index') }}" class="nav-link text-white {{ request()->routeIs('paket.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam me-2"></i> Data Paket
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('paket.create') }}" class="nav-link text-white {{ request()->routeIs('paket.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle me-2"></i> Tambah Paket
                </a>
            </li>
        </ul>
    </nav>

    {{-- ============ KONTEN ============ --}}
    <div id="page-content" class="flex-grow-1">

        <nav class="navbar navbar-expand bg-white border-bottom sticky-top px-3">
            <button class="btn btn-outline-secondary d-lg-none" id="btnToggleSidebar">
                <i class="bi bi-list"></i>
            </button>

            <div class="ms-auto d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary" id="btnDarkMode" title="Mode gelap">
                    <i class="bi bi-moon-stars"></i>
                </button>

                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5"></i>
                        <span class="d-none d-sm-inline">{{ auth()->user()->name ?? 'Petugas' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a href="{{ route('profile.edit') }}" class="dropdown-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                                <i class="bi bi-person-gear me-2"></i> Profil Saya
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="p-3 p-md-4">
            {{-- Breadcrumb --}}
            @hasSection('breadcrumb')
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-0">
                        @yield('breadcrumb')
                    </ol>
                </nav>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<!-- jQuery (dibutuhkan DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables + Buttons + Responsive extension -->
<script src="https://cdn.datatables.net/2.3.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.8/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.8/js/responsive.bootstrap5.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Chart.js (grafik dashboard) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

<script>
    // Sisipkan token CSRF ke semua request AJAX (jQuery)
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Toggle sidebar (tampilan mobile/tablet)
    document.getElementById('btnToggleSidebar')?.addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('show');
    });

    // Close sidebar
    document.getElementById('btnCloseSidebar')?.addEventListener('click', function () {
        const sidebar = document.getElementById('sidebar');
        // Di mobile: tutup dengan remove class 'show'
        // Di desktop: collapse sidebar dengan class 'collapsed'
        if (window.innerWidth < 992) {
            sidebar.classList.remove('show');
        } else {
            sidebar.classList.toggle('collapsed');
            document.getElementById('wrapper').classList.toggle('sidebar-collapsed');
        }
    });

    // Dark mode toggle
    const htmlEl = document.documentElement;
    const btnDark = document.getElementById('btnDarkMode');
    const savedTheme = window.__theme || 'light'; // disimpan di variabel JS (artifact tidak boleh pakai localStorage)
    btnDark?.addEventListener('click', function () {
        const current = htmlEl.getAttribute('data-bs-theme');
        htmlEl.setAttribute('data-bs-theme', current === 'dark' ? 'light' : 'dark');
    });

    // Tampilkan toast otomatis dari session flash Laravel
    @if (session('success'))
        Swal.fire({
            toast: true, position: 'top-end', icon: 'success',
            title: @json(session('success')), showConfirmButton: false, timer: 3000, timerProgressBar: true,
        });
    @endif
    @if (session('error'))
        Swal.fire({
            toast: true, position: 'top-end', icon: 'error',
            title: @json(session('error')), showConfirmButton: false, timer: 3000, timerProgressBar: true,
        });
    @endif
</script>

<script src="{{ asset('js/paket-app.js') }}"></script>
@stack('scripts')
</body>
</html>
