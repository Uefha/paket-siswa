/**
 * paket-app.js
 * Kumpulan interaksi front-end untuk halaman Data Paket:
 *  - Inisialisasi DataTables (server-side processing)
 *  - Live search & filter (status, kelas, kompi, ekspedisi, bulan, tahun)
 *  - Preview foto sebelum diunggah (form tambah/edit)
 *  - Konfirmasi hapus data & tandai sudah diambil (SweetAlert2)
 */

let tabelPaket = null;

function ambilFilterAktif() {
    return {
        status: $('#filterStatus').val() || '',
        kelas: $('#filterKelas').val() || '',
        kompi: $('#filterKompi').val() || '',
        ekspedisi: $('#filterEkspedisi').val() || '',
        bulan: $('#filterBulan').val() || '',
        tahun: $('#filterTahun').val() || '',
        dari: $('#filterDari').val() || '',
        sampai: $('#filterSampai').val() || '',
    };
}

function initDataTablePaket() {
    const $tabel = $('#tabelPaket');
    if ($tabel.length === 0) return;

    tabelPaket = $tabel.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: $tabel.data('url'),
            data: function (d) {
                return Object.assign(d, ambilFilterAktif());
            },
        },
        // Kolom "Foto", "Status", "Lama Menunggu", dan "Aksi" berisi HTML, jadi tidak boleh di-escape.
        // responsivePriority mengatur kolom mana yang tetap tampil duluan di layar sempit (HP) —
        // angka lebih kecil = lebih diutamakan tetap terlihat. Nama Siswa, Status, Lama Menunggu,
        // dan terutama Aksi (tombol edit/lihat/hapus/tandai) diprioritaskan supaya tidak perlu
        // geser tabel ke kanan lagi di HP; kolom lain otomatis disembunyikan ke tombol "+".
        columns: [
            { data: 'foto', orderable: false, searchable: false, responsivePriority: 5 },
            { data: 'nama_siswa', responsivePriority: 1 },
            { data: 'kelas', responsivePriority: 7 },
            { data: 'kompi', responsivePriority: 8 },
            { data: 'nama_pengirim', responsivePriority: 9 },
            { data: 'ekspedisi', responsivePriority: 10 },
            { data: 'nomor_resi', responsivePriority: 11 },
            { data: 'tanggal_datang', responsivePriority: 6 },
            { data: 'lama_menunggu', orderable: false, responsivePriority: 3 },
            { data: 'status', orderable: true, responsivePriority: 2 },
            { data: 'tanggal_diambil', responsivePriority: 12 },
            { data: 'aksi', orderable: false, searchable: false, responsivePriority: 1 },
        ],
        responsive: {
            details: {
                type: 'inline', // ikon "+" ditempel di kolom Nama Siswa (lihat target di bawah)
                target: 1,
            },
        },
        order: [[7, 'desc']],
        language: {
            processing: 'Memproses...',
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
            infoEmpty: 'Menampilkan 0 - 0 dari 0 data',
            infoFiltered: '(disaring dari _MAX_ total data)',
            loadingRecords: 'Memuat...',
            zeroRecords: 'Data tidak ditemukan',
            emptyTable: 'Tidak ada data yang tersedia',
            paginate: {
                first: 'Awal',
                previous: 'Sebelumnya',
                next: 'Berikutnya',
                last: 'Akhir',
            },
            aria: {
                sortAscending: ': urutkan naik',
                sortDescending: ': urutkan turun',
            },
        },
        dom: '<"row mb-2"<"col-sm-6"l><"col-sm-6 text-end"B>>rtip',
        buttons: [
            { extend: 'print', text: '<i class="bi bi-printer"></i> Print', className: 'btn btn-sm btn-outline-secondary' },
        ],
        drawCallback: function () {
            // Tampilkan empty state kustom bila tidak ada data pada halaman ini
            const info = tabelPaket.page.info();
            $('#emptyStatePaket').toggleClass('d-none', info.recordsDisplay !== 0);
        },
    });

    // Live search: kirim ulang query tiap kali admin mengetik (dengan sedikit jeda/debounce)
    let timerSearch = null;
    $('#pencarianPaket').on('keyup', function () {
        clearTimeout(timerSearch);
        const nilai = this.value;
        timerSearch = setTimeout(() => tabelPaket.search(nilai).draw(), 350);
    });

    // Filter dropdown & rentang tanggal -> reload data dari server tiap kali berubah
    $('#filterStatus, #filterKelas, #filterKompi, #filterEkspedisi, #filterBulan, #filterTahun, #filterDari, #filterSampai')
        .on('change', function () {
            tabelPaket.draw();
        });

    $('#btnResetTanggal').on('click', function () {
        $('#filterDari, #filterSampai').val('');
        tabelPaket.draw();
    });
}

function pasangTombolExport() {
    // Tombol export mengikuti filter yang sedang aktif di tabel
    $('#btnExportExcel, #btnExportPdf').on('click', function (e) {
        e.preventDefault();
        const params = new URLSearchParams(ambilFilterAktif());
        const pencarian = $('#pencarianPaket').val();
        if (pencarian) params.set('search', pencarian);

        const baseUrl = $(this).data('url');
        window.location.href = baseUrl + '?' + params.toString();
    });
}

function pasangKonfirmasiHapus() {
    $(document).on('click', '.btn-hapus-paket', function () {
        const url = $(this).data('url');
        const nama = $(this).data('nama');

        Swal.fire({
            title: 'Hapus data paket?',
            html: `Data paket atas nama <b>${nama}</b> akan dihapus permanen beserta foto-nya.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545',
        }).then((hasil) => {
            if (!hasil.isConfirmed) return;

            $.ajax({
                url: url,
                type: 'DELETE',
                success: function (res) {
                    Swal.fire({ icon: 'success', title: res.message, timer: 2000, showConfirmButton: false });
                    if (tabelPaket) tabelPaket.draw();
                },
                error: function () {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus data.', 'error');
                },
            });
        });
    });
}

function pasangTandaiDiambil() {
    $(document).on('click', '.btn-tandai-diambil', function () {
        const url = $(this).data('url');
        const nama = $(this).data('nama');

        Swal.fire({
            title: 'Tandai sudah diambil?',
            html: `Paket atas nama <b>${nama}</b> akan ditandai sebagai <b>Sudah Diambil</b> hari ini.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, tandai',
            cancelButtonText: 'Batal',
        }).then((hasil) => {
            if (!hasil.isConfirmed) return;

            $.ajax({
                url: url,
                type: 'PATCH',
                success: function (res) {
                    Swal.fire({ icon: 'success', title: res.message, timer: 2000, showConfirmButton: false })
                        .then(() => {
                            // Di halaman daftar cukup redraw tabel; di halaman lain (mis. detail) reload penuh
                            // supaya badge status & tombol yang tampil ikut ter-update.
                            if (tabelPaket) {
                                tabelPaket.draw();
                            } else {
                                window.location.reload();
                            }
                        });
                },
                error: function (xhr) {
                    const pesan = xhr.responseJSON?.message || 'Terjadi kesalahan.';
                    Swal.fire('Gagal', pesan, 'error');
                },
            });
        });
    });
}

/**
 * Preview foto sebelum di-submit, dipasang pada form tambah/edit paket.
 * Menampilkan pratinjau gambar & memvalidasi ekstensi/ukuran di sisi klien
 * (validasi final tetap dilakukan di server / Form Request).
 */
function pasangPreviewFoto() {
    const input = document.getElementById('inputFotoPaket');
    const preview = document.getElementById('previewFoto');
    if (!input || !preview) return;

    input.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) {
            preview.style.display = 'none';
            return;
        }

        const tipeDiizinkan = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!tipeDiizinkan.includes(file.type)) {
            Swal.fire('Format tidak didukung', 'Foto hanya boleh berformat JPG, JPEG, atau PNG.', 'error');
            input.value = '';
            preview.style.display = 'none';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            Swal.fire('Ukuran terlalu besar', 'Ukuran foto maksimal 5 MB.', 'error');
            input.value = '';
            preview.style.display = 'none';
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });
}

/**
 * Loading spinner sederhana saat form (dengan upload foto) di-submit,
 * supaya petugas tahu proses unggah sedang berjalan.
 */
function pasangLoadingSubmit() {
    $('form.form-paket').on('submit', function () {
        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...');
    });
}

/**
 * ============================================
 * FITUR KAMERA — ambil foto paket langsung dari kamera perangkat
 * ============================================
 * Memakai getUserMedia (butuh HTTPS atau localhost). Hasil jepretan
 * diubah jadi File lalu "disuntikkan" ke input#inputFotoPaket yang sudah
 * ada, supaya preview & validasi (pasangPreviewFoto) otomatis jalan tanpa
 * kode terpisah.
 */
let streamKamera = null;
let facingModeSaatIni = 'environment'; // utamakan kamera belakang di HP

function tampilkanErrorKamera(pesan) {
    const errorBox = document.getElementById('kameraError');
    if (!errorBox) return;
    errorBox.textContent = pesan;
    errorBox.classList.remove('d-none');
}

function hentikanKamera() {
    if (streamKamera) {
        streamKamera.getTracks().forEach((track) => track.stop());
        streamKamera = null;
    }
}

function bukaKamera(facingMode) {
    const video = document.getElementById('videoKamera');
    const canvas = document.getElementById('canvasKamera');
    const errorBox = document.getElementById('kameraError');
    if (!video) return;

    facingModeSaatIni = facingMode || facingModeSaatIni;

    errorBox.classList.add('d-none');
    errorBox.textContent = '';
    hentikanKamera();

    // Reset tampilan ke mode "live preview" (bukan hasil jepretan)
    video.classList.remove('d-none');
    canvas.classList.add('d-none');
    document.getElementById('btnAmbilGambar').classList.remove('d-none');
    document.getElementById('btnGunakanFoto').classList.add('d-none');
    document.getElementById('btnAmbilUlang').classList.add('d-none');

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        tampilkanErrorKamera('Browser ini tidak mendukung akses kamera. Silakan pilih file foto secara manual.');
        return;
    }

    navigator.mediaDevices.getUserMedia({
        video: {
            facingMode: { ideal: facingModeSaatIni },
            width: { ideal: 1280 },
            height: { ideal: 720 },
        },
        audio: false,
    })
        .then(function (stream) {
            streamKamera = stream;
            video.srcObject = stream;
        })
        .catch(function (err) {
            let pesan = 'Tidak bisa mengakses kamera. Pastikan izin kamera sudah diberikan.';
            if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                pesan = 'Akses kamera ditolak. Izinkan akses kamera pada browser, lalu coba lagi.';
            } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                pesan = 'Kamera tidak ditemukan pada perangkat ini.';
            } else if (location.protocol !== 'https:' && !['localhost', '127.0.0.1'].includes(location.hostname)) {
                pesan = 'Akses kamera hanya bisa dipakai lewat HTTPS (atau localhost saat development).';
            }
            tampilkanErrorKamera(pesan);
        });
}

function ambilGambarDariVideo() {
    const video = document.getElementById('videoKamera');
    const canvas = document.getElementById('canvasKamera');
    if (!video || !canvas || !video.videoWidth) return;

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

    video.classList.add('d-none');
    canvas.classList.remove('d-none');

    document.getElementById('btnAmbilGambar').classList.add('d-none');
    document.getElementById('btnAmbilUlang').classList.remove('d-none');
    document.getElementById('btnGunakanFoto').classList.remove('d-none');
}

function gunakanFotoHasilKamera() {
    const canvas = document.getElementById('canvasKamera');
    const inputFoto = document.getElementById('inputFotoPaket');
    if (!canvas || !inputFoto) return;

    canvas.toBlob(function (blob) {
        if (!blob) {
            Swal.fire('Gagal', 'Gagal memproses hasil jepretan, silakan coba lagi.', 'error');
            return;
        }

        const file = new File([blob], 'kamera-' + Date.now() + '.jpg', { type: 'image/jpeg' });

        try {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            inputFoto.files = dataTransfer.files;
            // Trigger event 'change' supaya preview & validasi di pasangPreviewFoto() otomatis jalan
            inputFoto.dispatchEvent(new Event('change'));
        } catch (e) {
            Swal.fire('Tidak didukung', 'Browser ini tidak mendukung pengisian file otomatis dari kamera. Silakan gunakan browser terbaru (Chrome/Firefox/Edge).', 'error');
            return;
        }

        const modalEl = document.getElementById('modalKamera');
        bootstrap.Modal.getInstance(modalEl)?.hide();
    }, 'image/jpeg', 0.9);
}

function pasangModalKamera() {
    const modalEl = document.getElementById('modalKamera');
    if (!modalEl) return; // halaman ini tidak punya form foto (mis. bukan create/edit)

    modalEl.addEventListener('shown.bs.modal', () => bukaKamera());
    modalEl.addEventListener('hidden.bs.modal', () => hentikanKamera());

    document.getElementById('btnAmbilGambar')?.addEventListener('click', ambilGambarDariVideo);
    document.getElementById('btnAmbilUlang')?.addEventListener('click', () => bukaKamera());
    document.getElementById('btnGunakanFoto')?.addEventListener('click', gunakanFotoHasilKamera);
    document.getElementById('btnGantiKamera')?.addEventListener('click', function () {
        bukaKamera(facingModeSaatIni === 'environment' ? 'user' : 'environment');
    });
}

$(function () {
    initDataTablePaket();
    pasangTombolExport();
    pasangKonfirmasiHapus();
    pasangTandaiDiambil();
    pasangPreviewFoto();
    pasangLoadingSubmit();
    pasangModalKamera();
});
