@extends('layouts.app')

@section('title', 'Detail Pengajuan - ' . $submission->no_pengajuan)
@section('page-title', 'Detail Pengajuan KPR Nasabah')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-12 col-md-8">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="badge bg-primary text-uppercase">{{ $submission->no_pengajuan }}</span>
                    <h4 class="fw-bold text-dark mt-1">Status Pengajuan KPR Anda</h4>
                </div>
                <div>
                    @if($submission->status_keputusan === 'DITERIMA')
                        <span class="badge-success-custom fs-5"><i class="fa-solid fa-circle-check me-1"></i> DITERIMA</span>
                    @elseif($submission->status_keputusan === 'TIDAK DITERIMA')
                        <span class="badge-danger-custom fs-5"><i class="fa-solid fa-circle-xmark me-1"></i> TIDAK DITERIMA</span>
                    @else
                        <span class="badge-warning-custom fs-6"><i class="fa-solid fa-clock me-1"></i> In Process</span>
                    @endif
                </div>
            </div>

            <div class="p-3 bg-light rounded-4 border mb-4">
                <div class="row text-center small">
                    <div class="col-4">
                        <div class="text-muted">Harga Rumah</div>
                        <div class="fw-bold text-dark">Rp {{ number_format($submission->harga_rumah, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted">Uang Muka (DP)</div>
                        <div class="fw-bold text-dark">Rp {{ number_format($submission->uang_muka_dp, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted">Nilai Pinjaman</div>
                        <div class="fw-bold text-primary">Rp {{ number_format($submission->nilai_pinjaman, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-robot text-primary me-2"></i> Hasil Analisis SMART Engine:</h6>
            <div class="p-3 bg-light rounded-3 border mb-4 small">
                <div class="mb-2"><strong>Skor Kelayakan SMART:</strong> <span class="fs-5 fw-bold text-primary">{{ number_format($submission->final_smart_score ?? 0, 2) }}</span> / 100</div>
                @if(isset($submission->smartResult->explanations['summary']))
                    <ul class="ps-3 text-muted mb-0">
                        @foreach($submission->smartResult->explanations['summary'] as $reason)
                            <li>{{ $reason }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @if($submission->manager_notes)
                <div class="alert alert-info border-0 shadow-sm p-3 small mb-0">
                    <strong>Catatan Keputusan Manager:</strong> {{ $submission->manager_notes }}
                </div>
            @endif
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card-custom p-4 h-100 text-center">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-file-pdf text-danger me-2"></i> Cetak Dokumen Resmi</h6>
            <p class="text-muted small mb-4">Unduh Surat Keputusan Hasil Analisis Kelayakan KPR resmi ber-QR Code & Tanda Tangan Digital.</p>

            <a href="{{ route('admin.history.pdf', $submission->id) }}" class="btn btn-danger btn-lg w-100 rounded-3 shadow mb-2">
                <i class="fa-solid fa-download me-2"></i> Unduh Hasil PDF
            </a>
            <a href="{{ route('admin.history.stream', $submission->id) }}" target="_blank" class="btn btn-outline-secondary w-100 rounded-3">
                <i class="fa-solid fa-eye me-2"></i> Preview Surat Decision
            </a>
        </div>
    </div>
</div>
@endsection
