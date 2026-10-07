<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Hasil Analisis Kelayakan KPR - {{ $submission->no_pengajuan }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px double #1e293b;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .bank-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .bank-subtitle {
            font-size: 9pt;
            color: #475569;
        }

        .doc-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 15px;
            text-decoration: underline;
            color: #0f172a;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1e3a8a;
            background: #f1f5f9;
            padding: 6px 10px;
            border-left: 4px solid #2563eb;
            margin-top: 15px;
            margin-bottom: 10px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.data-table td {
            padding: 5px 8px;
            vertical-align: top;
        }

        table.grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.grid-table th, table.grid-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            font-size: 10pt;
        }

        table.grid-table th {
            background-color: #f8fafc;
            font-weight: bold;
            text-align: center;
            color: #1e293b;
        }

        .decision-box {
            border: 2px solid #2563eb;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            margin-top: 15px;
            margin-bottom: 15px;
            background: #f8fafc;
        }

        .decision-accepted {
            border-color: #16a34a;
            background: #f0fdf4;
            color: #15803d;
        }

        .decision-rejected {
            border-color: #dc2626;
            background: #fef2f2;
            color: #b91c1c;
        }

        .decision-warning {
            border-color: #d97706;
            background: #fffbeb;
            color: #b45309;
        }

        .decision-status {
            font-size: 18pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .reason-box {
            background: #fafafa;
            border: 1px solid #e2e8fo;
            border-radius: 6px;
            padding: 10px;
            font-size: 9.5pt;
        }

        .footer-table {
            width: 100%;
            margin-top: 30px;
        }

        .qr-placeholder {
            width: 80px;
            height: 80px;
            border: 1px dashed #94a3b8;
            text-align: center;
            line-height: 80px;
            font-size: 8pt;
            color: #64748b;
            margin: 0 auto;
        }
    </style>
</head>
<body>

    <!-- Header Logo & Bank Info -->
    <table class="header-table">
        <tr>
            <td width="70%">
                <div class="bank-title">{{ $bankName }}</div>
                <div class="bank-subtitle">{{ $bankAddress }}</div>
                <div class="bank-subtitle">Telepon: {{ $bankPhone }} | Website: www.banksejahtera.co.id</div>
            </td>
            <td width="30%" style="text-align: right; vertical-align: middle;">
                <div style="font-weight: bold; font-size: 12pt; color: #2563eb;">KPR SMART SYSTEM</div>
                <div style="font-size: 8pt; color: #64748b;">No. Dok: {{ $submission->no_pengajuan }}</div>
            </td>
        </tr>
    </table>

    <div class="doc-title">SURAT KEPUTUSAN ANALISIS KELAYAKAN KPR</div>

    <!-- Applicant Bio Info -->
    <div class="section-title">1. DATA CALON NASABAH & PENGAJUAN</div>
    <table class="data-table">
        <tr>
            <td width="20%"><strong>Nama Nasabah</strong></td>
            <td width="30%">: {{ $submission->user->name }}</td>
            <td width="20%"><strong>Nomor Pengajuan</strong></td>
            <td width="30%">: {{ $submission->no_pengajuan }}</td>
        </tr>
        <tr>
            <td><strong>NIK / KTP</strong></td>
            <td>: {{ $submission->user->profile->nik ?? '-' }}</td>
            <td><strong>Harga Rumah</strong></td>
            <td>: Rp {{ number_format($submission->harga_rumah, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Pekerjaan</strong></td>
            <td>: {{ $submission->user->profile->pekerjaan ?? '-' }} ({{ $submission->user->profile->status_pekerjaan ?? '-' }})</td>
            <td><strong>Uang Muka (DP)</strong></td>
            <td>: Rp {{ number_format($submission->uang_muka_dp, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Penghasilan Total</strong></td>
            <td>: Rp {{ number_format(($submission->user->profile->penghasilan_bulanan ?? 0) + ($submission->user->profile->penghasilan_pasangan ?? 0), 0, ',', '.') }}</td>
            <td><strong>Plafon Pinjaman</strong></td>
            <td>: Rp {{ number_format($submission->nilai_pinjaman, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Riwayat SLIK</strong></td>
            <td>: {{ $submission->c1_verified_at ? $submission->c1_riwayat_kredit : 'Belum dikonfirmasi admin' }}</td>
            <td><strong>Tenor Pinjaman</strong></td>
            <td>: {{ $submission->tenor_tahun }} Tahun</td>
        </tr>
    </table>

    <!-- Decision Box Card -->
    @php
          $decision = $submission->smartResult->decision ?? 'PENDING';
          $decisionClass = 'decision-rejected';
          if ($decision === 'LAYAK') $decisionClass = 'decision-accepted';
          elseif ($decision === 'DIPERTIMBANGKAN') $decisionClass = 'decision-warning';
      @endphp
      <div class="decision-box {{ $decisionClass }}">
        <div style="font-size: 10pt; text-transform: uppercase; font-weight: bold; margin-bottom: 4px;">HASIL REKOMENDASI MESIN SMART</div>
        <div class="decision-status">{{ $decision }}</div>
        <div style="font-size: 11pt; margin-top: 4px;">Total Skor SMART: <strong>{{ number_format($submission->smartResult->total_score ?? 0, 2) }} / 1.00</strong></div>
    </div>

    <!-- SMART Calculation Matrix -->
    <div class="section-title">2. PERHITUNGAN SMART (SIMPLE MULTI ATTRIBUTE RATING TECHNIQUE)</div>
    <table class="grid-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Kriteria Evaluasi</th>
                <th>Tipe</th>
                <th>Bobot Awal</th>
                <th>Bobot Normalisasi (Wj)</th>
                <th>Nilai Utility ui(ai)</th>
                <th>Nilai Terbobot (Wj × ui)</th>
            </tr>
        </thead>
        <tbody>
            @if($submission->smartResult)
                @foreach($submission->smartResult->initial_weights as $code => $initWeight)
                    @php
                        $criterionObj = $criteria[$code] ?? null;
                        $normWeight = $submission->smartResult->normalized_weights[$code] ?? 0;
                        $utility = $submission->smartResult->utilities[$code] ?? 0;
                        $weighted = $submission->smartResult->weighted_scores[$code] ?? 0;
                    @endphp
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $code }}</td>
                        <td>{{ $criterionObj ? $criterionObj->name : $code }}</td>
                        <td style="text-align: center; text-transform: uppercase;">{{ $criterionObj ? $criterionObj->type : 'benefit' }}</td>
                        <td style="text-align: center;">{{ number_format($initWeight, 2) }}%</td>
                        <td style="text-align: center;">{{ number_format($normWeight, 4) }}</td>
                        <td style="text-align: center;">{{ number_format($utility, 2) }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ number_format($weighted, 2) }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr style="background: #f1f5f9; font-weight: bold;">
                <td colspan="4" style="text-align: right;">TOTAL:</td>
                <td style="text-align: center;">1.0000</td>
                <td style="text-align: center;">-</td>
                <td style="text-align: center; font-size: 11pt; color: #2563eb;">{{ number_format($submission->smartResult->total_score ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Automated Reasoning Breakdown -->
    <div class="section-title">3. ANALISIS & ALASAN REKOMENDASI MESIN SMART</div>
    <div class="reason-box">
        @if(isset($submission->smartResult->explanations['summary']))
            <ul style="margin: 0; padding-left: 18px;">
                @foreach($submission->smartResult->explanations['summary'] as $reason)
                    <li style="margin-bottom: 4px;">{{ $reason }}</li>
                @endforeach
            </ul>
        @else
            <div>Analisis sistem SMART telah selesai dengan skor akhir {{ $submission->smartResult->total_score ?? 0 }}.</div>
        @endif
    </div>

    @if($submission->manager_notes)
        <div style="margin-top: 10px; padding: 8px; background: #fffbe6; border: 1px solid #ffe58f; border-radius: 6px; font-size: 9.5pt;">
            <strong>Catatan Manager KPR:</strong> {{ $submission->manager_notes }}
        </div>
    @endif

    <!-- Signatures & Verification Code -->
    <table class="footer-table">
        <tr>
            <td width="40%" style="text-align: center;">
                <div style="font-size: 8pt; color: #64748b; margin-bottom: 5px;">QR Code Verifikasi Keaslian</div>
                <div class="qr-placeholder" style="border: none; padding: 0;">
                    <img src="data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate(route('admin.history.stream', $submission->id))) }}" alt="QR Code">
                </div>
                <div style="font-size: 7pt; color: #94a3b8; margin-top: 4px;">Hash: {{ substr(md5($submission->id . $submission->no_pengajuan), 0, 16) }}</div>
            </td>
            <td width="20%"></td>
            <td width="40%" style="text-align: center;">
                <div style="font-size: 9pt;">Jakarta, {{ $generatedAt }}</div>
                <div style="font-size: 9pt; font-weight: bold; margin-top: 2px;">{{ $jabatanTtd }}</div>
                <div style="height: 70px; margin-top: 10px;">
                    <!-- Ruang kosong untuk tanda tangan basah -->
                </div>
                <div style="font-weight: bold; font-size: 9.5pt; text-decoration: underline;">{{ $submission->approver->name ?? $namaTtd }}</div>
                @if($nipTtd)
                <div style="font-size: 8pt; color: #64748b;">NIP: {{ $nipTtd }}</div>
                @endif
            </td>
        </tr>
    </table>

</body>
</html>
