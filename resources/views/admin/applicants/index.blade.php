@extends('layouts.app')

@section('title', 'Data Calon Nasabah KPR')
@section('page-title', 'Kelola Data Calon Nasabah KPR')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-1">Daftar Pengajuan Calon Nasabah</h5>
            <div class="text-muted small">Kelola seluruh biodata & pengajuan KPR nasabah.</div>
        </div>

        <a href="{{ route('admin.applicants.create') }}" class="btn btn-primary rounded-3 px-3 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Tambah Nasabah Baru
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <form action="{{ route('admin.applicants.index') }}" method="GET" class="row g-2 mb-4">
        <div class="col-12 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Cari NIK, Nama, atau No. Registrasi...">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <select name="status" class="form-select">
                <option value="">-- All Status Keputusan --</option>
                <option value="DITERIMA" {{ request('status') === 'DITERIMA' ? 'selected' : '' }}>DITERIMA</option>
                <option value="TIDAK DITERIMA" {{ request('status') === 'TIDAK DITERIMA' ? 'selected' : '' }}>TIDAK DITERIMA</option>
            </select>
        </div>
        <div class="col-12 col-md-2">
            <button type="submit" class="btn btn-secondary w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light small text-uppercase fw-bold text-secondary">
                <tr>
                    <th>No</th>
                    <th>No. Pengajuan</th>
                    <th>Nama / NIK</th>
                    <th>Pekerjaan & Penghasilan</th>
                    <th>Nilai Pinjaman</th>
                    <th>Skor SMART</th>
                    <th>Status Keputusan</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $index => $sub)
                    <tr>
                        <td>{{ $submissions->firstItem() + $index }}</td>
                        <td>
                            <span class="fw-bold text-primary">{{ $sub->no_pengajuan }}</span><br>
                            <small class="text-muted">{{ $sub->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $sub->user?->name ?? 'Nasabah' }}</div>
                            <small class="text-muted">NIK: {{ $sub->user?->profile?->nik ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $sub->user?->profile?->pekerjaan ?? '-' }}</div>
                            <small class="text-success fw-bold">Rp {{ number_format(($sub->user?->profile?->penghasilan_bulanan ?? 0) + ($sub->user?->profile?->penghasilan_pasangan ?? 0), 0, ',', '.') }}/bln</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">Rp {{ number_format($sub->nilai_pinjaman, 0, ',', '.') }}</div>
                            <small class="text-muted">Tenor {{ $sub->tenor_tahun }} Tahun</small>
                        </td>
                        <td>
                            @if($sub->final_smart_score)
                                <span class="badge bg-primary bg-opacity-10 text-primary fs-6 px-3 py-2 fw-bold">
                                    {{ number_format($sub->final_smart_score, 2) }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted border">Belum dihitung</span>
                            @endif
                        </td>
                        <td>
                            @if($sub->status_keputusan === 'DITERIMA')
                                <span class="badge-success-custom text-nowrap"><i class="fa-solid fa-circle-check me-1"></i> DITERIMA</span>
                            @elseif($sub->status_keputusan === 'TIDAK DITERIMA')
                                <span class="badge-danger-custom text-nowrap"><i class="fa-solid fa-circle-xmark me-1"></i> TIDAK DITERIMA</span>
                            @else
                                <span class="badge-warning-custom text-nowrap"><i class="fa-solid fa-clock me-1"></i> Menunggu</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('admin.applicants.show', ['applicant' => $sub->id]) }}" class="btn btn-sm btn-light border text-primary" title="Detail Analysis"><i class="fa-solid fa-eye"></i></a>
                                <a href="{{ route('admin.applicants.edit', ['applicant' => $sub->id]) }}" class="btn btn-sm btn-light border text-warning" title="Edit Data"><i class="fa-solid fa-pen"></i></a>
                                <a href="{{ route('admin.history.pdf', ['submission' => $sub->id]) }}" class="btn btn-sm btn-light border text-danger" title="Download PDF"><i class="fa-solid fa-file-pdf"></i></a>
                                <form action="{{ route('admin.applicants.destroy', ['applicant' => $sub->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pengajuan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-folder-open fs-1 mb-2 d-block opacity-50"></i>
                            Tidak ada data calon nasabah yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $submissions->links() }}
    </div>
</div>
@endsection
