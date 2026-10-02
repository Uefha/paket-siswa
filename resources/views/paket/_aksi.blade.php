{{-- Kolom aksi di tabel data paket. Di-render dari controller (server-side) --}}
<div class="d-flex gap-1">
    <a href="{{ route('paket.show', $paket) }}" class="btn btn-sm btn-outline-primary" title="Detail">
        <i class="bi bi-eye"></i>
    </a>
    <a href="{{ route('paket.edit', $paket) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
        <i class="bi bi-pencil"></i>
    </a>

    @if ($paket->status === \App\Models\Paket::STATUS_BELUM)
        <button type="button"
                class="btn btn-sm btn-outline-success btn-tandai-diambil"
                title="Tandai sudah diambil"
                data-url="{{ route('paket.tandaiDiambil', $paket) }}"
                data-nama="{{ $paket->nama_siswa }}">
            <i class="bi bi-check2-circle"></i>
        </button>
    @endif

    <button type="button"
            class="btn btn-sm btn-outline-danger btn-hapus-paket"
            title="Hapus"
            data-url="{{ route('paket.destroy', $paket) }}"
            data-nama="{{ $paket->nama_siswa }}">
        <i class="bi bi-trash"></i>
    </button>
</div>
