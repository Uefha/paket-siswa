<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Paket</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #212529; }
        h2, h4 { margin: 0; }
        .header { text-align: center; margin-bottom: 16px; }
        .header h4 { margin-top: 4px; color: #555; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #999; padding: 5px 6px; text-align: left; }
        th { background-color: #f1f3f5; }
        .text-center { text-align: center; }
        .badge-sudah { color: #146c43; font-weight: bold; }
        .badge-terlambat { color: #b02a37; font-weight: bold; }
        .badge-belum { color: #997404; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 10px; color: #777; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Data Paket Siswa</h2>
        <h4>SMA Taruna Nusantara IKN</h4>
        @if ($dari || $sampai)
            <p>Periode: {{ $dari ?: '...' }} s/d {{ $sampai ?: '...' }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Graha</th>
                <th>Pengirim</th>
                <th>Ekspedisi</th>
                <th>No. Resi</th>
                <th>Tgl Datang</th>
                <th class="text-center">Lama Menunggu</th>
                <th>Status</th>
                <th>Tgl Diambil</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pakets as $i => $paket)
                @php $badge = $paket->badge_status; @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $paket->nama_siswa }}</td>
                    <td>{{ $paket->kelas ?: '-' }}</td>
                    <td>{{ $paket->kompi ?: '-' }}</td>
                    <td>{{ $paket->nama_pengirim ?: '-' }}</td>
                    <td>{{ $paket->ekspedisi ?: '-' }}</td>
                    <td>{{ $paket->nomor_resi ?: '-' }}</td>
                    <td>{{ $paket->tanggal_datang ? $paket->tanggal_datang->format('d-m-Y') : '-' }}</td>
                    <td class="text-center">{{ $paket->lama_menunggu }} hari</td>
                    <td class="{{ $paket->status === \App\Models\Paket::STATUS_SUDAH ? 'badge-sudah' : ($paket->terlambat ? 'badge-terlambat' : 'badge-belum') }}">
                        {{ $badge['label'] }}
                    </td>
                    <td>{{ $paket->tanggal_diambil ? $paket->tanggal_diambil->format('d-m-Y') : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="11" class="text-center">Tidak ada data paket.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak pada {{ $dicetak }}</div>
</body>
</html>
