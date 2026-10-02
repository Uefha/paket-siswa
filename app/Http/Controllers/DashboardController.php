<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ---- Card statistik ----
        $totalPaket       = Paket::count();
        $sudahDiambil     = Paket::where('status', Paket::STATUS_SUDAH)->count();
        $belumDiambil     = Paket::where('status', Paket::STATUS_BELUM)->count();
        $paketHariIni     = Paket::whereDate('tanggal_datang', Carbon::today())->count();

        // Paket yang sudah lewat batas 3 hari dan masih belum diambil
        $paketTerlambatQuery = Paket::where('status', Paket::STATUS_BELUM)
            ->whereDate('tanggal_datang', '<=', Carbon::today()->subDays(Paket::BATAS_HARI_WARNING));

        $jumlahTerlambat = (clone $paketTerlambatQuery)->count();

        // ---- Daftar notifikasi: paket yang harus segera diambil (urut paling lama menunggu) ----
        $notifikasiTerlambat = (clone $paketTerlambatQuery)
            ->orderBy('tanggal_datang')
            ->take(10)
            ->get();

        // ---- Grafik jumlah paket per bulan (12 bulan terakhir) ----
        $awalPeriode = Carbon::now()->subMonths(11)->startOfMonth();

        $rekapBulanan = Paket::query()
            ->select(
                DB::raw('YEAR(tanggal_datang) as tahun'),
                DB::raw('MONTH(tanggal_datang) as bulan'),
                DB::raw('COUNT(*) as jumlah')
            )
            ->where('tanggal_datang', '>=', $awalPeriode)
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get()
            ->keyBy(fn ($row) => $row->tahun . '-' . $row->bulan);

        $labelGrafik = [];
        $dataGrafik  = [];
        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        for ($i = 0; $i < 12; $i++) {
            $tanggal = $awalPeriode->copy()->addMonths($i);
            $key = $tanggal->year . '-' . $tanggal->month;
            $labelGrafik[] = $namaBulan[$tanggal->month] . ' ' . $tanggal->format('y');
            $dataGrafik[]  = $rekapBulanan[$key]->jumlah ?? 0;
        }

        return view('dashboard', compact(
            'totalPaket',
            'sudahDiambil',
            'belumDiambil',
            'paketHariIni',
            'jumlahTerlambat',
            'notifikasiTerlambat',
            'labelGrafik',
            'dataGrafik'
        ));
    }
}
