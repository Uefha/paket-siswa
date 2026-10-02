<?php

namespace App\Exports;

use App\Http\Controllers\PaketController;
use App\Models\Paket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaketExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(private array $filter = [])
    {
    }

    public function collection()
    {
        $controller = new PaketController();

        return $controller->queryUntukExport($this->filter)->get();
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Kelas',
            'Graha',
            'Nama Pengirim',
            'Ekspedisi',
            'Nomor Resi',
            'Tanggal Datang',
            'Lama Menunggu (Hari)',
            'Status',
            'Tanggal Diambil',
            'Keterangan',
        ];
    }

    public function map($paket): array
    {
        /** @var Paket $paket */
        return [
            $paket->nama_siswa,
            $paket->kelas ?: '-',
            $paket->kompi ?: '-',
            $paket->nama_pengirim ?: '-',
            $paket->ekspedisi ?: '-',
            $paket->nomor_resi ?: '-',
            $paket->tanggal_datang ? $paket->tanggal_datang->format('d-m-Y') : '-',
            $paket->lama_menunggu,
            $paket->badge_status['label'],
            $paket->tanggal_diambil ? $paket->tanggal_diambil->format('d-m-Y') : '-',
            $paket->keterangan ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Tebalkan baris judul kolom agar laporan lebih rapi
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
