<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengajuan KPR</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; margin: 0; }
        .subtitle { font-size: 14px; margin: 5px 0 0 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge { padding: 3px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .layak { color: #0f5132; }
        .tidak-layak { color: #842029; }
        .dipertimbangkan { color: #664d03; }
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 10px; color: #777; }
        .signature { margin-top: 50px; text-align: right; }
        .signature p { margin: 0; }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">{{ \App\Models\Setting::getByKey('bank_name', 'PT Citra Pasada Properti') }}</p>
        <p class="subtitle">{{ \App\Models\Setting::getByKey('bank_address', 'Jl. Jenderal Sudirman') }} | Telp: {{ \App\Models\Setting::getByKey('bank_phone', '123') }}</p>
        <h3 style="margin-top: 15px;">LAPORAN KESELURUHAN PENGAJUAN KPR (SMART)</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>No. Pengajuan</th>
                <th>Nama Nasabah (NIK)</th>
                <th>Pekerjaan</th>
                <th>Plafon KPR (Rp)</th>
                <th>Skor SMART</th>
                <th>Rekomendasi</th>
                <th>Keputusan Manager</th>
            </tr>
        </thead>
        <tbody>
            @foreach($submissions as $index => $sub)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $sub->no_pengajuan }}</td>
                <td>
                    <b>{{ $sub->user->name }}</b><br>
                    <small>NIK: {{ $sub->user->profile?->nik ?? '-' }}</small>
                </td>
                <td>{{ $sub->user->profile?->pekerjaan ?? '-' }}</td>
                <td class="text-right">{{ number_format($sub->nilai_pinjaman, 0, ',', '.') }}</td>
                <td class="text-center">{{ $sub->smartResult?->total_score ?? '-' }}</td>
                <td class="text-center">
                    @php
                        $rek = $sub->smartResult?->decision ?? '-';
                        $class = $rek === 'LAYAK' ? 'layak' : ($rek === 'TIDAK LAYAK' ? 'tidak-layak' : 'dipertimbangkan');
                    @endphp
                    <span class="{{ $class }}">{{ $rek }}</span>
                </td>
                <td class="text-center">
                    @if($sub->status_pengajuan === 'approved')
                        <span class="layak">DISETUJUI</span>
                    @elseif($sub->status_pengajuan === 'rejected')
                        <span class="tidak-layak">DITOLAK</span>
                    @else
                        <span>MENUNGGU</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <p>Kuningan, {{ date('d F Y') }}</p>
        <br><br><br>
        <p><b>Manager / Pimpinan</b></p>
    </div>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }} oleh Sistem Pendukung Keputusan KPR Method SMART
    </div>
</body>
</html>
