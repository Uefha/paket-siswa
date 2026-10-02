<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class Paket extends Model
{
    use HasFactory;

    /**
     * Konstanta status agar tidak ada "magic string" tersebar di kode.
     */
    public const STATUS_BELUM = 'Belum Diambil';
    public const STATUS_SUDAH = 'Sudah Diambil';

    /**
     * Batas hari sebelum paket dianggap terlambat diambil.
     */
    public const BATAS_HARI_WARNING = 3;

    protected $fillable = [
        'nama_siswa',
        'kelas',
        'kompi',
        'nama_pengirim',
        'ekspedisi',
        'nomor_resi',
        'foto_paket',
        'tanggal_datang',
        'tanggal_diambil',
        'status',
        'diambil_oleh',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_datang'  => 'date',
        'tanggal_diambil' => 'date',
    ];

    /**
     * Relasi ke user (petugas) yang memproses pengambilan paket.
     */
    public function petugasPengambilan()
    {
        return $this->belongsTo(User::class, 'diambil_oleh');
    }

    /**
     * Accessor: selisih hari sejak paket datang sampai hari ini
     * (atau sampai tanggal diambil jika statusnya sudah diambil).
     * Tidak disimpan ke database — dihitung setiap saat data dibuka.
     */
    public function getLamaMenungguAttribute(): int
    {
        // Jika tanggal_datang tidak diisi (opsional), tidak ada dasar untuk menghitung
        if (! $this->tanggal_datang) {
            return 0;
        }

        $sampai = $this->status === self::STATUS_SUDAH && $this->tanggal_diambil
            ? $this->tanggal_diambil
            : Carbon::today();

        return (int) $this->tanggal_datang->copy()->startOfDay()->diffInDays($sampai->copy()->startOfDay(), false);
    }

    /**
     * Accessor: apakah paket ini sudah melewati batas waktu (>= 3 hari)
     * dan statusnya masih "Belum Diambil".
     */
    public function getTerlambatAttribute(): bool
    {
        return $this->status === self::STATUS_BELUM
            && $this->lama_menunggu >= self::BATAS_HARI_WARNING;
    }

    /**
     * Accessor: informasi badge (label, warna, ikon) untuk ditampilkan di view.
     * Memusatkan logika tampilan status di satu tempat agar konsisten
     * di seluruh halaman (index, detail, dashboard, notifikasi).
     */
    public function getBadgeStatusAttribute(): array
    {
        if ($this->status === self::STATUS_SUDAH) {
            return [
                'label' => 'Sudah Diambil',
                'class' => 'bg-success',
                'icon'  => 'bi-check-circle-fill',
            ];
        }

        if ($this->terlambat) {
            return [
                'label' => 'Terlambat Diambil (' . $this->lama_menunggu . ' Hari)',
                'class' => 'bg-danger',
                'icon'  => 'bi-exclamation-triangle-fill',
            ];
        }

        return [
            'label' => 'Belum Diambil',
            'class' => 'bg-warning text-dark',
            'icon'  => 'bi-clock-fill',
        ];
    }

    /**
     * Accessor: URL foto paket (memakai Storage disk 'public').
     */
    public function getFotoUrlAttribute(): string
    {
        return $this->foto_paket ? Storage::url($this->foto_paket) : '';
    }

    /**
     * Tandai paket sebagai sudah diambil.
     * Otomatis mengisi tanggal_diambil hari ini & petugas yang memproses.
     */
    public function tandaiSudahDiambil(?int $userId = null): void
    {
        $this->update([
            'status'          => self::STATUS_SUDAH,
            'tanggal_diambil' => Carbon::today(),
            'diambil_oleh'    => $userId,
        ]);
    }

    /**
     * Hapus file foto dari storage saat data paket dihapus.
     * Dipasang lewat model event "deleting" via boot().
     */
    protected static function booted(): void
    {
        static::deleting(function (Paket $paket) {
            if ($paket->foto_paket && Storage::disk('public')->exists($paket->foto_paket)) {
                Storage::disk('public')->delete($paket->foto_paket);
            }
        });
    }

    /* =========================================================
     |  QUERY SCOPES — dipakai untuk pencarian & filter
     * =========================================================*/

    public function scopeCari(Builder $query, ?string $keyword): Builder
    {
        if (! $keyword) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('nama_siswa', 'like', "%{$keyword}%")
              ->orWhere('nomor_resi', 'like', "%{$keyword}%")
              ->orWhere('nama_pengirim', 'like', "%{$keyword}%")
              ->orWhere('kelas', 'like', "%{$keyword}%")
              ->orWhere('kompi', 'like', "%{$keyword}%")
              ->orWhere('ekspedisi', 'like', "%{$keyword}%");
        });
    }

    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if (! $status) {
            return $query;
        }

        if ($status === 'terlambat') {
            return $query->where('status', self::STATUS_BELUM)
                ->whereDate('tanggal_datang', '<=', Carbon::today()->subDays(self::BATAS_HARI_WARNING));
        }

        return $query->where('status', $status);
    }

    public function scopeFilterKelas(Builder $query, ?string $kelas): Builder
    {
        return $kelas ? $query->where('kelas', $kelas) : $query;
    }

    public function scopeFilterKompi(Builder $query, ?string $kompi): Builder
    {
        return $kompi ? $query->where('kompi', $kompi) : $query;
    }

    public function scopeFilterEkspedisi(Builder $query, ?string $ekspedisi): Builder
    {
        return $ekspedisi ? $query->where('ekspedisi', $ekspedisi) : $query;
    }

    public function scopeFilterBulanTahun(Builder $query, ?string $bulan, ?string $tahun): Builder
    {
        if ($bulan) {
            $query->whereMonth('tanggal_datang', $bulan);
        }
        if ($tahun) {
            $query->whereYear('tanggal_datang', $tahun);
        }

        return $query;
    }

    public function scopeFilterRentangTanggal(Builder $query, ?string $dari, ?string $sampai): Builder
    {
        if ($dari) {
            $query->whereDate('tanggal_datang', '>=', $dari);
        }
        if ($sampai) {
            $query->whereDate('tanggal_datang', '<=', $sampai);
        }

        return $query;
    }
}
