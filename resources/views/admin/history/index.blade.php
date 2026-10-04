@extends('layouts.app')

@section('title', 'Riwayat Analisis SMART & Laporan')
@section('page-title', 'Riwayat Analisis SMART & Cetak Laporan PDF')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-1">Riwayat Perhitungan & Keputusan SMART</h5>
            <div class="text-muted small">Cetak atau unduh sertifikat laporan hasil analisis KPR resmi.</div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle datatable-custom">
            <thead class="table-light small text-uppercase fw-bold">
                <tr>
                    <th>No. Pengajuan</th>
                    <th>Nama Nasabah</th>
                    <th>Tanggal Analisis</th>
                    <th>Skor SMART</th>
                    <th>Status Decision</th>
                    <th>Status ACC Manager</th>
                    <th>Disetujui Oleh</th>
                    <th class="text-center">Aksi Laporan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $item)
                    <tr>
                        <td><span class="fw-bold text-primary">{{ $item->no_pengajuan }}</span></td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $item->user->name }}</div>
                            <small class="text-muted">NIK: {{ $item->user->profile->nik ?? '-' }}</small>
                        </td>
                        <td>{{ $item->smartResult ? $item->smartResult->analyzed_at->translatedFormat('d F Y H:i') : $item->updated_at->format('d/m/Y') }}</td>
                        <td>
                            <span class="fw-bold fs-6 text-primary">{{ number_format($item->final_smart_score ?? 0, 2) }}</span>
                        </td>
                        <td>
                            @if($item->status_keputusan === 'LAYAK')
                                <span class="badge-success-custom">LAYAK</span>
                            @elseif($item->status_keputusan === 'DIPERTIMBANGKAN')
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill small fw-bold border">DIPERTIMBANGKAN</span>
                            @else
                                <span class="badge-danger-custom">TIDAK LAYAK</span>
                            @endif
                        </td>
                        <td>
                            @if($item->status_pengajuan === 'approved')
                                <span class="badge bg-success text-nowrap"><i class="fa-solid fa-check-double me-1"></i> ACC Manager</span>
                            @elseif($item->status_pengajuan === 'rejected')
                                <span class="badge bg-danger text-nowrap"><i class="fa-solid fa-xmark me-1"></i> Ditolak Manager</span>
                            @else
                                <span class="badge bg-warning text-dark text-nowrap"><i class="fa-solid fa-clock me-1"></i> Menunggu ACC</span>
                            @endif
                        </td>
                        <td>
                            <small class="fw-semibold text-dark">{{ $item->approver->name ?? 'System Auto' }}</small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('admin.history.stream', $item->id) }}" target="_blank" class="btn btn-sm btn-light border text-primary" title="Preview PDF"><i class="fa-solid fa-eye me-1"></i> Preview</a>
                                <a href="{{ route('admin.history.pdf', $item->id) }}" class="btn btn-sm btn-danger shadow-sm" title="Download PDF"><i class="fa-solid fa-file-pdf me-1"></i> Unduh PDF</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
