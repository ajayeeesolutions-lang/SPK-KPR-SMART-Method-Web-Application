@extends('layouts.app')

@section('title', 'Detail Analisis SMART - ' . $submission->no_pengajuan)
@section('page-title', 'Detail Analisis SMART & Profil Nasabah')

@section('content')
<div class="row g-3 mb-4">
    <!-- Left Column: Nasabah Profile Summary -->
    <div class="col-12 col-lg-4">
        <div class="card-custom p-4 h-100 text-center">
            <div class="mb-3">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center border border-primary border-opacity-25" style="width: 90px; height: 90px;">
                    <i class="fa-solid fa-user-tie fs-1"></i>
                </div>
            </div>

            <h5 class="fw-bold text-dark mb-1">{{ $submission->user?->name ?? 'Nasabah' }}</h5>
            <div class="badge bg-light text-secondary border mb-3">{{ $submission->no_pengajuan }}</div>

            <div class="text-start small border-top pt-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">NIK</span>
                    <span class="fw-bold text-dark">{{ $submission->user?->profile?->nik ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Pekerjaan</span>
                    <span class="fw-bold text-dark">{{ $submission->user?->profile?->pekerjaan ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Status Pekerjaan</span>
                    <span class="badge bg-info text-dark">{{ $submission->user?->profile?->status_pekerjaan ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Penghasilan Total</span>
                    <span class="fw-bold text-success">Rp {{ number_format(($submission->user?->profile?->penghasilan_bulanan ?? 0) + ($submission->user?->profile?->penghasilan_pasangan ?? 0), 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Lama Bekerja</span>
                    <span class="fw-bold text-dark">{{ round(($submission->user?->profile?->lama_bekerja_bulan ?? 0)/12, 1) }} Tahun</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Rasio Cicilan (DTI)</span>
                    <span class="fw-bold text-danger">{{ $submission->user?->profile?->dti_ratio ?? 0 }}%</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Riwayat SLIK</span>
                    <span class="fw-bold text-primary">{{ $submission->user?->profile?->riwayat_kredit ?? '-' }}</span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-grid gap-2">
                <a href="{{ route('admin.history.pdf', $submission->id) }}" class="btn btn-danger rounded-3" target="_blank"><i class="fa-solid fa-file-pdf me-2"></i> Cetak Laporan PDF</a>
            </div>
        </div>
    </div>

    <!-- Right Column: Decision Score Card & Explanation -->
    <div class="col-12 col-lg-8">
        <div class="card-custom p-4 h-100">
            <div class="row align-items-center mb-4">
                <div class="col-12 col-md-7">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Keputusan Akhir Mesin SMART</span>
                    @if($submission->status_keputusan === 'DITERIMA')
                        <h2 class="fw-bold text-success mb-1"><i class="fa-solid fa-circle-check me-2"></i> DITERIMA</h2>
                        <p class="text-muted small mb-0">Nasabah dinyatakan <strong>LAYAK</strong> memperoleh fasilitas KPR.</p>
                    @elseif($submission->status_keputusan === 'TIDAK DITERIMA')
                        <h2 class="fw-bold text-danger mb-1"><i class="fa-solid fa-circle-xmark me-2"></i> TIDAK DITERIMA</h2>
                        <p class="text-muted small mb-0">Nasabah <strong>BELUM MEMENUHI</strong> standar kelayakan KPR.</p>
                    @else
                        <h2 class="fw-bold text-warning mb-1"><i class="fa-solid fa-clock me-2"></i> MENUNGGU ANALISIS</h2>
                    @endif
                </div>

                <div class="col-12 col-md-5 text-md-end mt-3 mt-md-0">
                    <div class="p-3 bg-light rounded-4 border text-center d-inline-block" style="min-width: 180px;">
                        <small class="text-muted fw-bold d-block">SKOR SMART AKHIR</small>
                        <span class="display-5 fw-bold text-primary">{{ number_format($submission->final_smart_score ?? 0, 2) }}</span>
                        <small class="text-muted d-block">/ 100.00 (Threshold 80)</small>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-robot text-primary me-2"></i> Alasan & Analisis Mesin SMART</h6>
            <div class="p-3 bg-light rounded-3 border mb-4">
                @if(isset($submission->smartResult->explanations['summary']))
                    <ul class="mb-0 ps-3 small text-secondary">
                        @foreach($submission->smartResult->explanations['summary'] as $reason)
                            <li class="mb-1">{{ $reason }}</li>
                        @endforeach
                    </ul>
                @else
                    <small class="text-muted">Analisis detail belum di-generate.</small>
                @endif
            </div>

            <!-- Financial Plafon Info -->
            <div class="row g-2 text-center small">
                <div class="col-4">
                    <div class="p-2 border rounded bg-white">
                        <div class="text-muted">Harga Rumah</div>
                        <div class="fw-bold text-dark">Rp {{ number_format($submission->harga_rumah, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 border rounded bg-white">
                        <div class="text-muted">Uang Muka (DP)</div>
                        <div class="fw-bold text-dark">Rp {{ number_format($submission->uang_muka_dp, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 border rounded bg-white">
                        <div class="text-muted">Nilai Pinjaman</div>
                        <div class="fw-bold text-primary">Rp {{ number_format($submission->nilai_pinjaman, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Steps Matrix -->
<div class="card-custom p-4 mb-4">
    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-table text-primary me-2"></i> Matriks Perhitungan Transparan SMART</h5>

    @if($submission->smartResult)
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center small fw-bold">
                    <tr>
                        <th>Kode</th>
                        <th>Kriteria Evaluasi</th>
                        <th>Bobot Awal</th>
                        <th>Bobot Normalisasi (w_j)</th>
                        <th>Nilai Utility (u_j)</th>
                        <th>Skor Terbobot (w_j × u_j)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submission->smartResult->initial_weights as $code => $initW)
                        @php
                            $normW = $submission->smartResult->normalized_weights[$code] ?? 0;
                            $u = $submission->smartResult->utilities[$code] ?? 0;
                            $weighted = $submission->smartResult->weighted_scores[$code] ?? 0;
                        @endphp
                        <tr>
                            <td class="text-center fw-bold text-primary">{{ $code }}</td>
                            <td>{{ $code }} - Kriteria Evaluasi</td>
                            <td class="text-center">{{ number_format($initW, 2) }}%</td>
                            <td class="text-center">{{ number_format($normW, 4) }}</td>
                            <td class="text-center fw-bold text-dark">{{ number_format($u, 2) }}</td>
                            <td class="text-center fw-bold text-success fs-6">{{ number_format($weighted, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">TOTAL BOBOT & SKOR:</td>
                        <td class="text-center">1.0000</td>
                        <td class="text-center">-</td>
                        <td class="text-center text-primary fs-5">{{ number_format($submission->final_smart_score ?? 0, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @else
        <div class="alert alert-warning text-center">Analisis SMART belum dijalankan untuk pengajuan ini.</div>
    @endif
</div>
@endsection
