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
            <form action="{{ route('admin.smart.engine') }}" method="GET" class="d-flex gap-2">
                <select name="submission_id" class="form-select" onchange="this.form.submit()">
                    @foreach($submissions as $sub)
                        <option value="{{ $sub->id }}" {{ ($activeSubmission && $activeSubmission->id == $sub->id) ? 'selected' : '' }}>
                            {{ $sub->no_pengajuan }} - {{ $sub->user?->name ?? 'Nasabah' }} (Skor: {{ number_format($sub->final_smart_score ?? 0, 2) }})
                        </option>
                    @endforeach
                </select>
                @if($activeSubmission)
                    <form action="{{ route('admin.smart.analyze', ['submission' => $activeSubmission->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary text-nowrap shadow-sm"><i class="fa-solid fa-rotate me-1"></i> Hitung Ulang</button>
                    </form>
                @endif
            </form>
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
            <h6 class="fw-bold text-dark mb-3"><span class="badge bg-primary me-2">Tahap 1 & 2</span> Bobot Awal & Normalisasi Bobot</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle small">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Kode</th>
                            <th>Kriteria</th>
                            <th>Bobot Awal (W)</th>
                            <th>Bobot Normalisasi (w_j)</th>
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
            <div class="small text-muted fst-italic">Formula Normalisasi: w_j = W_j / Total_W</div>
        </div>
    </div>

    <!-- Step 3 & 4: Utility & Calculation -->
    <div class="col-12 col-lg-7">
        <div class="card-custom p-4 h-100">
            <h6 class="fw-bold text-dark mb-3"><span class="badge bg-primary me-2">Tahap 3 & 4</span> Nilai Utility & Perkalian (w_j × u_j)</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle small">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Kode</th>
                            <th>Nilai Nasabah</th>
                            <th>Sub Kriteria Terpenuhi</th>
                            <th>Utility (u_j)</th>
                            <th>Skor Terbobot (w_j × u_j)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($criteria as $c)
                            @php
                                $u = $smartResult->utilities[$c->code] ?? 0;
                                $wNorm = $smartResult->normalized_weights[$c->code] ?? 0;
                                $score = $smartResult->weighted_scores[$c->code] ?? 0;
                                $reason = $smartResult->explanations[$c->code] ?? '';
                            @endphp
                            <tr>
                                <td class="text-center font-monospace fw-bold">{{ $c->code }}</td>
                                <td class="text-muted">{{ $reason }}</td>
                                <td><span class="badge bg-light text-dark border">Matched Rule</span></td>
                                <td class="text-center fw-bold">{{ number_format($u, 2) }}</td>
                                <td class="text-center fw-bold text-success fs-6">{{ number_format($score, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold text-center">
                        <tr>
                            <td colspan="4" class="text-end">TOTAL SKOR SMART:</td>
                            <td class="text-primary fs-5">{{ number_format($smartResult->total_score, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Step 5 & 6: System Rankings & Final Decision Card -->
<div class="row g-3 mb-4">
    <!-- Step 5: Rankings Table -->
    <div class="col-12 col-lg-7">
        <div class="card-custom p-4 h-100">
            <h6 class="fw-bold text-dark mb-3"><span class="badge bg-primary me-2">Tahap 5</span> Ranking Kelayakan Seluruh Pengajuan</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle small">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Rank</th>
                            <th>No. Pengajuan</th>
                            <th>Nama Nasabah</th>
                            <th>Skor SMART</th>
                            <th>Keputusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rankings as $idx => $rnk)
                            <tr class="{{ $rnk->id == $activeSubmission->id ? 'table-primary fw-bold' : '' }}">
                                <td class="text-center">
                                    @if($idx == 0)
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-crown me-1"></i> 1</span>
                                    @else
                                        <span class="fw-bold">{{ $idx + 1 }}</span>
                                    @endif
                                </td>
                                <td>{{ $rnk->no_pengajuan }}</td>
                                <td>{{ $rnk->user?->name ?? 'Nasabah' }}</td>
                                <td class="text-center fw-bold text-primary">{{ number_format($rnk->final_smart_score, 2) }}</td>
                                <td class="text-center">
                                    @if($rnk->status_keputusan === 'DITERIMA')
                                        <span class="badge-success-custom text-nowrap">DITERIMA</span>
                                    @else
                                        <span class="badge-danger-custom text-nowrap">TIDAK DITERIMA</span>
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

            <div class="p-3 mb-3 text-center rounded-4 border {{ $smartResult->decision === 'DITERIMA' ? 'bg-success bg-opacity-10 border-success' : 'bg-danger bg-opacity-10 border-danger' }}">
                <small class="text-muted fw-bold d-block text-uppercase">Status Rekomendasi</small>
                <h2 class="fw-bold {{ $smartResult->decision === 'DITERIMA' ? 'text-success' : 'text-danger' }} mb-1">
                    {{ $smartResult->decision }}
                </h2>
                <div class="fs-5 fw-bold text-dark">Skor: {{ number_format($smartResult->total_score, 2) }}</div>
                <small class="text-muted">Ambang Batas Threshold System: {{ number_format($threshold, 2) }}</small>
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
