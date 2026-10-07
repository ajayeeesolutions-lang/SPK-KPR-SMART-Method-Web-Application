@extends('layouts.app')

@section('title', 'Mesin SMART Engine & Transparency')
@section('page-title', 'Proses Analisis Transparan Metode SMART')

@section('content')
<!-- Selector Submission & Wizard Banner -->
<div class="card-custom p-4 mb-4">
    <div class="row align-items-center g-3">
        <div class="col-12 col-md-6">
            <h5 class="fw-bold text-dark mb-1"><i class="fa-solid fa-microchip text-primary me-2"></i> Engine Transparansi Perhitungan SMART</h5>
            <p class="text-muted small mb-0">Pilih pengajuan calon nasabah untuk melihat 6 tahapan perhitungan SMART secara mendalam.</p>
        </div>
        <div class="col-12 col-md-6">
            <div class="d-flex flex-column flex-sm-row gap-2">
                <form action="{{ route('admin.smart.engine') }}" method="GET" class="flex-grow-1">
                    <select name="submission_id" class="form-select" onchange="this.form.submit()">
                        @foreach($submissions as $sub)
                            <option value="{{ $sub->id }}" {{ ($activeSubmission && $activeSubmission->id == $sub->id) ? 'selected' : '' }}>
                                {{ $sub->no_pengajuan }} - {{ $sub->user?->name ?? 'Nasabah' }} (Skor: {{ $sub->c1_verified_at ? number_format($sub->final_smart_score ?? 0, 2) : 'Menunggu SLIK' }})
                            </option>
                        @endforeach
                    </select>
                </form>
                @if($activeSubmission)
                    <form action="{{ route('admin.smart.analyze_all') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-dark text-nowrap shadow-sm"><i class="fa-solid fa-rotate-right me-1"></i> Hitung Ulang Semua</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- SMART Wizard Step Bar -->
<div class="card-custom p-4 mb-4">
    <div class="smart-wizard-nav">
        <div class="wizard-step-item completed">
            <div class="wizard-step-circle">1</div>
            <div class="wizard-step-label">Bobot Awal</div>
        </div>
        <div class="wizard-step-item completed">
            <div class="wizard-step-circle">2</div>
            <div class="wizard-step-label">Normalisasi</div>
        </div>
        <div class="wizard-step-item completed">
            <div class="wizard-step-circle">3</div>
            <div class="wizard-step-label">Nilai Utility</div>
        </div>
        <div class="wizard-step-item completed">
            <div class="wizard-step-circle">4</div>
            <div class="wizard-step-label">Perhitungan</div>
        </div>
        <div class="wizard-step-item completed">
            <div class="wizard-step-circle">5</div>
            <div class="wizard-step-label">Ranking</div>
        </div>
        <div class="wizard-step-item active">
            <div class="wizard-step-circle">6</div>
            <div class="wizard-step-label">Keputusan</div>
        </div>
    </div>
</div>

@if($activeSubmission && $smartResult)
<!-- Tahap 1, 2, 3 & 4 Matrix Tables -->
<div class="row g-3 mb-4">
    <!-- Step 1 & 2: Weights -->
    <div class="col-12 col-lg-5">
        <div class="card-custom p-4 h-100">
            <h6 class="fw-bold text-dark mb-1"><span class="badge bg-primary me-2">Tahap 1 & 2</span> Bobot Awal & Normalisasi Bobot</h6>
            <p class="text-muted small mb-3">Setiap bobot awal dibagi dengan total bobot agar jumlahnya = 1</p>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle small">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Kriteria</th>
                            <th>Bobot Awal<br><small class="fw-normal text-muted">(skala 0–100)</small></th>
                            <th>Bobot Normalisasi<br><small class="fw-normal text-muted">Wj = wj ÷ Σwj</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($criteria as $c)
                            @php
                                $wInit = $smartResult->initial_weights[$c->code] ?? $c->weight;
                                $wNorm = $smartResult->normalized_weights[$c->code] ?? 0;
                            @endphp
                            <tr>
                                <td class="text-center font-monospace fw-bold">{{ $c->code }}</td>
                                <td>{{ $c->name }}</td>
                                <td class="text-center">{{ number_format($wInit, 2) }}%</td>
                                <td class="text-center fw-bold text-primary">{{ number_format($wNorm, 4) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold text-center">
                        <tr>
                            <td colspan="2">TOTAL</td>
                            <td>{{ number_format(array_sum($smartResult->initial_weights), 2) }}%</td>
                            <td>1.0000</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="small text-muted fst-italic mt-1">
                <i class="fa-solid fa-circle-info text-primary me-1"></i>
                Rumus: Bobot Normalisasi (Wj) = Bobot Awal Kriteria (wj) ÷ Total Semua Bobot (Σwj)
            </div>
        </div>
    </div>

    <!-- Step 3 & 4: Utility & Calculation -->
    <div class="col-12 col-lg-7">
        <div class="card-custom p-4 h-100">
            <h6 class="fw-bold text-dark mb-1"><span class="badge bg-primary me-2">Tahap 3 & 4</span> Nilai Utility & Skor Terbobot</h6>
            <p class="text-muted small mb-3">Nilai utility tiap kriteria dikalikan bobot normalisasi, lalu dijumlahkan</p>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle small">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Kode</th>
                            <th>Data Nasabah & Subkriteria Terpenuhi</th>
                            <th>Nilai Utility<br><small class="fw-normal text-muted">ui(ai) = 0.0–1.0</small></th>
                            <th>Bobot Normalisasi<br><small class="fw-normal text-muted">(Wj)</small></th>
                            <th>Skor Terbobot<br><small class="fw-normal text-muted">Wj × ui(ai)</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($criteria as $c)
                            @php
                                $u     = $smartResult->utilities[$c->code] ?? 0;
                                $wNorm = $smartResult->normalized_weights[$c->code] ?? 0;
                                $score = $smartResult->weighted_scores[$c->code] ?? 0;
                                $reason = $smartResult->explanations[$c->code] ?? '-';
                                $uColor = $u >= 0.8 ? 'text-success' : ($u >= 0.6 ? 'text-warning' : 'text-danger');
                            @endphp
                            <tr>
                                <td class="text-center font-monospace fw-bold">{{ $c->code }}</td>
                                <td class="text-muted small">{{ $reason }}</td>
                                <td class="text-center fw-bold {{ $uColor }}">{{ number_format($u, 2) }}</td>
                                <td class="text-center text-primary fw-semibold">{{ number_format($wNorm, 4) }}</td>
                                <td class="text-center fw-bold text-success fs-6">{{ number_format($score, 4) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold text-center">
                        <tr>
                            <td colspan="4" class="text-end">
                                Total Skor SMART — u(ai) = Σ Wj × ui(ai) :
                            </td>
                            <td class="text-primary fs-5">{{ number_format($smartResult->total_score, 4) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="small text-muted fst-italic mt-1">
                <i class="fa-solid fa-circle-info text-primary me-1"></i>
                Rumus Skor Akhir: u(ai) = Σ [ Bobot Normalisasi (Wj) × Nilai Utility ui(ai) ]
            </div>
        </div>
    </div>
</div>

<!-- Step 5 & 6: System Rankings & Final Decision Card -->
<div class="row g-3 mb-4">
    <!-- Step 5: Rankings Table -->
    <div class="col-12 col-lg-7">
        <div class="card-custom p-4 h-100">
            <h6 class="fw-bold text-dark mb-3"><span class="badge bg-primary me-2">Tahap 5</span> Daftar Skor Kelayakan Pengajuan</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">No. Pengajuan</th>
                            <th>Nama Nasabah</th>
                            <th class="text-center">Skor SMART</th>
                            <th class="text-center">Keputusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rankings as $idx => $rnk)
                            <tr class="{{ $rnk->id == $activeSubmission->id ? 'table-primary fw-bold' : '' }}">
                                <td class="ps-3">{{ $rnk->no_pengajuan }}</td>
                                <td>{{ $rnk->user?->name ?? 'Nasabah' }}</td>
                                <td class="text-center fw-bold text-primary">{{ number_format($rnk->final_smart_score, 2) }}</td>
                                <td class="text-center">
                                    @if($rnk->status_keputusan === 'LAYAK')
                                        <span class="badge-success-custom text-nowrap">LAYAK</span>
                                    @elseif($rnk->status_keputusan === 'DIPERTIMBANGKAN')
                                        <span class="badge bg-warning text-dark text-nowrap">DIPERTIMBANGKAN</span>
                                    @else
                                        <span class="badge-danger-custom text-nowrap">TIDAK LAYAK</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Step 6: Decision Summary Card -->
    <div class="col-12 col-lg-5">
        <div class="card-custom p-4 h-100">
            <h6 class="fw-bold text-dark mb-3"><span class="badge bg-primary me-2">Tahap 6</span> Hasil Keputusan SMART System</h6>

            @php
                $bgClass = $smartResult->decision === 'LAYAK' ? 'bg-success border-success' : ($smartResult->decision === 'DIPERTIMBANGKAN' ? 'bg-warning border-warning' : 'bg-danger border-danger');
                $textClass = $smartResult->decision === 'LAYAK' ? 'text-success' : ($smartResult->decision === 'DIPERTIMBANGKAN' ? 'text-warning' : 'text-danger');
            @endphp
            <div class="p-3 mb-3 text-center rounded-4 border bg-opacity-10 {{ $bgClass }}">
                <small class="text-muted fw-bold d-block text-uppercase">Status Rekomendasi</small>
                <h2 class="fw-bold {{ $textClass }} mb-1">
                    {{ $smartResult->decision }}
                </h2>
                <div class="fs-5 fw-bold text-dark">Skor: {{ number_format($smartResult->total_score, 2) }}</div>
                <small class="text-muted">Ambang Batas Kelayakan: 0.80</small>
            </div>

            <div class="small">
                <div class="fw-bold text-dark mb-2"><i class="fa-solid fa-robot text-primary me-1"></i> Penjelasan Otomatis Sistem:</div>
                @if(isset($smartResult->explanations['summary']))
                    <ul class="ps-3 text-muted mb-0">
                        @foreach($smartResult->explanations['summary'] as $exp)
                            <li class="mb-1">{{ $exp }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection
