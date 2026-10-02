<?php

namespace App\Http\Controllers;

use App\Exports\PaketExport;
use App\Http\Requests\StorePaketRequest;
use App\Http\Requests\UpdatePaketRequest;
use App\Models\Paket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class PaketController extends Controller
{
    /**
     * Halaman utama data paket. Tabel-nya sendiri diisi lewat AJAX
     * ke method data() supaya pencarian/filter/sorting diproses di server
     * (lebih ringan untuk jumlah data yang besar).
     */
    public function index(Request $request)
    {
        $daftarKelas     = Paket::query()->select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        $daftarKompi     = Paket::query()->select('kompi')->distinct()->orderBy('kompi')->pluck('kompi');
        $daftarEkspedisi = Paket::query()->select('ekspedisi')->distinct()->orderBy('ekspedisi')->pluck('ekspedisi');

        return view('paket.index', compact('daftarKelas', 'daftarKompi', 'daftarEkspedisi'));
    }

    /**
     * Endpoint JSON untuk DataTables (server-side processing).
     * Menerima parameter standar DataTables (start, length, search, order)
     * ditambah filter kustom (status, kelas, kompi, ekspedisi, bulan, tahun).
     */
    public function data(Request $request)
    {
        $query = Paket::query()
            ->cari($request->input('search.value'))
            ->filterStatus($request->input('status'))
            ->filterKelas($request->input('kelas'))
            ->filterKompi($request->input('kompi'))
            ->filterEkspedisi($request->input('ekspedisi'))
            ->filterBulanTahun($request->input('bulan'), $request->input('tahun'))
            ->filterRentangTanggal($request->input('dari'), $request->input('sampai'));

        $recordsTotal    = Paket::count();
        $recordsFiltered = (clone $query)->count();

        // Kolom yang boleh dipakai untuk sorting, urut sesuai index kolom di tabel (view)
        $kolomSortable = [
            null, 'nama_siswa', 'kelas', 'kompi', 'nama_pengirim',
            'ekspedisi', 'nomor_resi', 'tanggal_datang', null, 'status', 'tanggal_diambil', null,
        ];

        if ($request->filled('order.0.column')) {
            $kolom = $kolomSortable[(int) $request->input('order.0.column')] ?? 'tanggal_datang';
            $arah  = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
            if ($kolom) {
                $query->orderBy($kolom, $arah);
            }
        } else {
            $query->orderByDesc('tanggal_datang');
        }

        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $pakets = $length > 0 ? $query->skip($start)->take($length)->get() : $query->get();

        $data = $pakets->map(function (Paket $paket) {
            $badge = $paket->badge_status;

            return [
                'foto' => $paket->foto_url
                    ? '<img src="' . $paket->foto_url . '" class="rounded" width="48" height="48" style="object-fit:cover" alt="Foto paket">'
                    : '<span class="text-muted small">Tidak ada</span>',
                'nama_siswa'     => e($paket->nama_siswa),
                'kelas'          => e($paket->kelas ?: '-'),
                'kompi'          => e($paket->kompi ?: '-'),
                'nama_pengirim'  => e($paket->nama_pengirim ?: '-'),
                'ekspedisi'      => e($paket->ekspedisi ?: '-'),
                'nomor_resi'     => e($paket->nomor_resi ?: '-'),
                'tanggal_datang' => $paket->tanggal_datang ? $paket->tanggal_datang->format('d-m-Y') : '-',
                'lama_menunggu'  => $paket->lama_menunggu . ' hari',
                'status'         => '<span class="badge ' . $badge['class'] . '"><i class="bi ' . $badge['icon'] . ' me-1"></i>' . $badge['label'] . '</span>',
                'tanggal_diambil' => $paket->tanggal_diambil ? $paket->tanggal_diambil->format('d-m-Y') : '-',
                'aksi' => view('paket._aksi', compact('paket'))->render(),
            ];
        });

        return response()->json([
            'draw'            => (int) $request->input('draw', 1),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function create()
    {
        return view('paket.create');
    }

    public function store(StorePaketRequest $request)
    {
        $data = $request->validated();
        $data['foto_paket'] = $this->simpanFoto($request);
        $data['status'] = Paket::STATUS_BELUM;
        // Tanggal datang boleh dikosongkan di form; jika kosong, anggap paket datang hari ini
        // supaya perhitungan "lama menunggu" tetap berjalan normal.
        $data['tanggal_datang'] = $data['tanggal_datang'] ?: now()->toDateString();

        Paket::create($data);

        return redirect()
            ->route('paket.index')
            ->with('success', 'Data paket berhasil disimpan.');
    }

    public function show(Paket $paket)
    {
        $paket->load('petugasPengambilan');

        return view('paket.show', compact('paket'));
    }

    public function edit(Paket $paket)
    {
        return view('paket.edit', compact('paket'));
    }

    public function update(UpdatePaketRequest $request, Paket $paket)
    {
        $data = $request->validated();

        // Jika field tanggal dikosongkan saat edit, pertahankan nilai yang sudah ada
        // (bukan ditimpa kosong) supaya perhitungan lama menunggu tidak terganggu.
        if (empty($data['tanggal_datang'])) {
            unset($data['tanggal_datang']);
        }

        if ($request->hasFile('foto_paket')) {
            // Hapus foto lama sebelum menyimpan yang baru
            if ($paket->foto_paket && Storage::disk('public')->exists($paket->foto_paket)) {
                Storage::disk('public')->delete($paket->foto_paket);
            }
            $data['foto_paket'] = $this->simpanFoto($request);
        }

        $paket->update($data);

        return redirect()
            ->route('paket.index')
            ->with('success', 'Data paket berhasil diperbarui.');
    }

    public function destroy(Paket $paket)
    {
        // Penghapusan file foto otomatis lewat model event di Paket::booted()
        $paket->delete();

        return response()->json(['success' => true, 'message' => 'Data paket berhasil dihapus.']);
    }

    /**
     * Ubah status paket menjadi "Sudah Diambil".
     * tanggal_diambil & petugas terisi otomatis (lihat Paket::tandaiSudahDiambil()).
     */
    public function tandaiDiambil(Request $request, Paket $paket)
    {
        if ($paket->status === Paket::STATUS_SUDAH) {
            return response()->json([
                'success' => false,
                'message' => 'Paket ini sudah tercatat diambil sebelumnya.',
            ], 422);
        }

        $paket->tandaiSudahDiambil($request->user()?->id);

        return response()->json([
            'success' => true,
            'message' => 'Paket atas nama ' . $paket->nama_siswa . ' ditandai sudah diambil.',
        ]);
    }

    /**
     * Export data paket (sesuai filter aktif) ke Excel.
     */
    public function exportExcel(Request $request)
    {
        $namaFile = 'data-paket-' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new PaketExport($this->filterUntukExport($request)), $namaFile);
    }

    /**
     * Export data paket (sesuai filter aktif / rentang tanggal) ke PDF.
     * Dipakai juga untuk fitur "Cetak laporan berdasarkan rentang tanggal".
     */
    public function exportPdf(Request $request)
    {
        $pakets = $this->queryUntukExport($this->filterUntukExport($request))->get();

        $pdf = Pdf::loadView('exports.paket-pdf', [
            'pakets'  => $pakets,
            'dari'    => $request->input('dari'),
            'sampai'  => $request->input('sampai'),
            'dicetak' => now()->format('d-m-Y H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-paket-' . now()->format('Y-m-d_His') . '.pdf');
    }

    /* =========================================================
     |  HELPER PRIVATE
     * =========================================================*/

    /**
     * Menyimpan file foto ke storage/app/public/paket dengan nama unik,
     * lalu mengembalikan path relatifnya untuk disimpan ke database.
     */
    private function simpanFoto(Request $request): string
    {
        $file    = $request->file('foto_paket');
        $namaFile = Str::uuid() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs('paket', $namaFile, 'public');
    }

    /**
     * Mengumpulkan parameter filter dari request (dipakai bersama oleh export Excel & PDF).
     */
    private function filterUntukExport(Request $request): array
    {
        return [
            'search'     => $request->input('search'),
            'status'     => $request->input('status'),
            'kelas'      => $request->input('kelas'),
            'kompi'      => $request->input('kompi'),
            'ekspedisi'  => $request->input('ekspedisi'),
            'bulan'      => $request->input('bulan'),
            'tahun'      => $request->input('tahun'),
            'dari'       => $request->input('dari'),
            'sampai'     => $request->input('sampai'),
        ];
    }

    /**
     * Query builder yang sudah diberi filter, dipakai bersama oleh PaketExport dan exportPdf().
     */
    public function queryUntukExport(array $filter)
    {
        return Paket::query()
            ->cari($filter['search'] ?? null)
            ->filterStatus($filter['status'] ?? null)
            ->filterKelas($filter['kelas'] ?? null)
            ->filterKompi($filter['kompi'] ?? null)
            ->filterEkspedisi($filter['ekspedisi'] ?? null)
            ->filterBulanTahun($filter['bulan'] ?? null, $filter['tahun'] ?? null)
            ->filterRentangTanggal($filter['dari'] ?? null, $filter['sampai'] ?? null)
            ->orderByDesc('tanggal_datang');
    }
}
