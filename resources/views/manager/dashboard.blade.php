@extends('layouts.app')

@section('title', 'Manager Executive Dashboard')
@section('page-title', 'Dashboard Eksekutif Manager KPR')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-4 col-xl">
        <div class="card-custom p-3 d-flex align-items-center flex-row gap-3">
            <div class="rounded-3 bg-secondary bg-opacity-10 text-secondary p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                <i class="fa-solid fa-clipboard-check fs-3"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Butuh Persetujuan</div>
                <h3 class="fw-bold mb-0 text-secondary">{{ $totalPendingApproval }}</h3>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-4 col-xl">
        <div class="card-custom p-3 d-flex align-items-center flex-row gap-3">
            <div class="rounded-3 bg-success bg-opacity-10 text-success p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                <i class="fa-solid fa-circle-check fs-3"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Layak</div>
                <h3 class="fw-bold mb-0 text-success">{{ $totalDiterima }}</h3>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-4 col-xl">
        <div class="card-custom p-3 d-flex align-items-center flex-row gap-3">
            <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                <i class="fa-solid fa-triangle-exclamation fs-3"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Dipertimbangkan</div>
                <h3 class="fw-bold mb-0 text-warning">{{ $totalDipertimbangkan }}</h3>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-4 col-xl">
        <div class="card-custom p-3 d-flex align-items-center flex-row gap-3">
            <div class="rounded-3 bg-danger bg-opacity-10 text-danger p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                <i class="fa-solid fa-circle-xmark fs-3"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Tidak Layak</div>
                <h3 class="fw-bold mb-0 text-danger">{{ $totalDitolak }}</h3>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-4 col-xl">
        <div class="card-custom p-3 d-flex align-items-center flex-row gap-3">
            <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                <i class="fa-solid fa-folder-open fs-3"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Total Pengajuan</div>
                <h3 class="fw-bold mb-0 text-primary">{{ $totalPengajuan }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-list-check text-primary me-2"></i> Pengajuan Butuh Persetujuan Akhir Manager</h6>
        <a href="{{ route('manager.submissions.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>No. Pengajuan</th>
                    <th>Nama Nasabah</th>
                    <th>Plafon Pinjaman</th>
                    <th>Skor SMART</th>
                    <th>Hasil Rekomendasi Engine</th>
                    <th class="text-center">Aksi Manager</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentSubmissions as $sub)
                    <tr>
                        <td><span class="fw-bold text-primary">{{ $sub->no_pengajuan }}</span></td>
                        <td>
                            <div class="fw-bold text-dark">{{ $sub->user->name }}</div>
                            <small class="text-muted">{{ $sub->user->profile->pekerjaan ?? '-' }}</small>
                        </td>
                        <td>Rp {{ number_format($sub->nilai_pinjaman, 0, ',', '.') }}</td>
                        <td><span class="fw-bold fs-6 text-primary">{{ number_format($sub->final_smart_score ?? 0, 2) }}</span></td>
                        <td>
                            @if($sub->status_keputusan === 'DITERIMA')
                                <span class="badge-success-custom">DITERIMA</span>
                            @else
                                <span class="badge-danger-custom">TIDAK DITERIMA</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('manager.submissions.show', $sub->id) }}" class="btn btn-sm btn-primary rounded-3 px-3 shadow-sm">
                                <i class="fa-solid fa-file-signature me-1"></i> Review & Decision
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada pengajuan yang menunggu approval.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
