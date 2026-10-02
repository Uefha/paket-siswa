{{-- Modal kamera — dipakai bersama oleh form Tambah & Edit Paket.
     Membutuhkan browser yang mendukung getUserMedia (Chrome/Firefox/Edge/Safari modern)
     dan diakses lewat HTTPS atau localhost (kebijakan keamanan browser). --}}
<div class="modal fade" id="modalKamera" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-camera-fill me-2"></i>Ambil Foto Paket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center">
                <div id="kameraError" class="alert alert-danger small d-none"></div>

                <video id="videoKamera" autoplay playsinline muted class="w-100 rounded bg-dark" style="max-height:360px;"></video>
                <canvas id="canvasKamera" class="w-100 rounded d-none" style="max-height:360px;"></canvas>
            </div>
            <div class="modal-footer justify-content-between flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnGantiKamera">
                    <i class="bi bi-arrow-repeat me-1"></i> Ganti Kamera
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary d-none" id="btnAmbilUlang">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Ambil Ulang
                    </button>
                    <button type="button" class="btn btn-primary" id="btnAmbilGambar">
                        <i class="bi bi-camera me-1"></i> Ambil Gambar
                    </button>
                    <button type="button" class="btn btn-success d-none" id="btnGunakanFoto">
                        <i class="bi bi-check-lg me-1"></i> Gunakan Foto Ini
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
