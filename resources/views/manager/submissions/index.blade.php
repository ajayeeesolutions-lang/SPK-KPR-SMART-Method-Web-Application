@extends('layouts.app')

@section('title', 'Persetujuan Manager KPR')
@section('page-title', 'Persetujuan & Verifikasi Akhir Manager')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-1">Seluruh Pengajuan KPR</h5>
            <div class="text-muted small">Tinjau hasil perhitungan SMART dan berikan persetujuan resmi.</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle datatable-custom">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>No. Pengajuan</th>
                    <th>Nama Nasabah</th>
                    <th>Plafon KPR</th>
                    <th>Skor SMART</th>
                    <th>Rekomendasi Engine</th>
                    <th>Status Pengajuan</th>
                    <th class="text-center">Aksi Manager</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submissions as $sub)
                    <tr>
                        <td><span class="fw-bold text-primary">{{ $sub->no_pengajuan }}</span></td>
                        <td>
                            <div class="fw-bold text-dark">{{ $sub->user?->name ?? 'Nasabah' }}</div>
                            <small class="text-muted">NIK: {{ $sub->user?->profile?->nik ?? '-' }}</small>
                        </td>
                        <td>Rp {{ number_format($sub->nilai_pinjaman, 0, ',', '.') }}</td>
                        <td><span class="fw-bold text-primary fs-6">{{ number_format($sub->final_smart_score ?? 0, 2) }}</span></td>
                        <td>
                            @if($sub->status_keputusan === 'LAYAK')
                                <span class="badge-success-custom text-nowrap">LAYAK</span>
                            @elseif($sub->status_keputusan === 'DIPERTIMBANGKAN')
                                <span class="badge bg-warning text-dark text-nowrap">DIPERTIMBANGKAN</span>
                            @else
                                <span class="badge-danger-custom text-nowrap">TIDAK LAYAK</span>
                            @endif
                        </td>
                        <td>
                            @if($sub->status_pengajuan === 'approved')
                                <span class="badge bg-success text-nowrap"><i class="fa-solid fa-check me-1"></i> Approved</span>
                            @elseif($sub->status_pengajuan === 'rejected')
                                <span class="badge bg-danger text-nowrap"><i class="fa-solid fa-xmark me-1"></i> Rejected</span>
                            @else
                                <span class="badge bg-warning text-dark text-nowrap"><i class="fa-solid fa-clock me-1"></i> Pending Review</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('manager.submissions.show', ['submission' => $sub->id]) }}" class="btn btn-sm btn-primary rounded-3 text-nowrap">
                                <i class="fa-solid fa-file-signature me-1"></i> Review Detail
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
