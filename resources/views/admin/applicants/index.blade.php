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

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('template.excel') }}" class="btn btn-outline-secondary rounded-3 shadow-sm">
                <i class="fa-solid fa-download me-1"></i> Template Excel
            </a>
            <a href="{{ route('admin.applicants.export') }}" class="btn btn-outline-success rounded-3 shadow-sm">
                <i class="fa-solid fa-file-excel me-1"></i> Export
            </a>
            <a href="{{ route('admin.applicants.export_pdf') }}" class="btn btn-outline-danger rounded-3 shadow-sm" target="_blank">
                <i class="fa-solid fa-file-pdf me-1"></i> PDF
            </a>
            <button type="button" class="btn btn-outline-info rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fa-solid fa-file-import me-1"></i> Import
            </button>
            <a href="{{ route('admin.applicants.create') }}" class="btn btn-primary rounded-3 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Tambah Baru
            </a>
        </div>
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
                <option value="LAYAK" {{ request('status') === 'LAYAK' ? 'selected' : '' }}>LAYAK</option>
                <option value="DIPERTIMBANGKAN" {{ request('status') === 'DIPERTIMBANGKAN' ? 'selected' : '' }}>DIPERTIMBANGKAN</option>
                <option value="TIDAK LAYAK" {{ request('status') === 'TIDAK LAYAK' ? 'selected' : '' }}>TIDAK LAYAK</option>
            </select>
        </div>
        <div class="col-12 col-md-2">
            <button type="submit" class="btn btn-secondary w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light small text-uppercase fw-bold text-secondary text-nowrap">
                <tr>
                    <th class="text-center">No</th>
                    <th>No. Pengajuan</th>
                    <th>Nama / NIK</th>
                    <th>Pekerjaan & Penghasilan</th>
                    <th>Nilai Pinjaman</th>
                    <th class="text-center">Status SLIK OJK</th>
                    <th class="text-center">Skor SMART</th>
                    <th class="text-center">Hasil SMART</th>
                    <th class="text-center">Status ACC Manager</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-nowrap" id="applicants-tbody">
                @forelse($submissions as $index => $sub)
                    <tr>
                        <td class="text-center">{{ $submissions->firstItem() + $index }}</td>
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
                        <td class="text-center">
                            @if($sub->c1_riwayat_kredit === 'Lancar')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success fw-bold">Lancar (Kol 1)</span>
                            @elseif($sub->c1_riwayat_kredit === 'Dalam Perhatian Khusus')
                                <span class="badge bg-info bg-opacity-10 text-info border border-info fw-bold">DPK (Kol 2)</span>
                            @elseif($sub->c1_riwayat_kredit === 'Kurang Lancar')
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning fw-bold">Kurang Lancar (Kol 3)</span>
                            @elseif($sub->c1_riwayat_kredit === 'Diragukan')
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger fw-bold">Diragukan (Kol 4)</span>
                            @elseif($sub->c1_riwayat_kredit === 'Macet')
                                <span class="badge bg-dark text-white fw-bold">Macet (Kol 5)</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border fw-bold">Belum Dicek</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($sub->c1_verified_at && $sub->final_smart_score !== null)
                                <span class="badge bg-primary bg-opacity-10 text-primary fs-6 px-3 py-1 fw-bold">
                                    {{ number_format($sub->final_smart_score, 2) }}
                                </span>
                            @elseif(!$sub->c1_verified_at)
                                <span class="badge bg-warning text-dark border">Menunggu konfirmasi SLIK</span>
                            @else
                                <span class="badge bg-light text-muted border">Belum dihitung</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($sub->c1_verified_at && $sub->status_keputusan === 'LAYAK')
                                <span class="badge-success-custom"><i class="fa-solid fa-circle-check me-1"></i> LAYAK</span>
                            @elseif($sub->c1_verified_at && $sub->status_keputusan === 'DIPERTIMBANGKAN')
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation me-1"></i> DIPERTIMBANGKAN</span>
                            @elseif($sub->c1_verified_at && $sub->status_keputusan === 'TIDAK LAYAK')
                                <span class="badge-danger-custom"><i class="fa-solid fa-circle-xmark me-1"></i> TIDAK LAYAK</span>
                            @else
                                <span class="badge-warning-custom"><i class="fa-solid fa-clock me-1"></i> {{ $sub->c1_verified_at ? 'Menunggu analisis' : 'Menunggu SLIK' }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($sub->c1_verified_at && $sub->status_pengajuan === 'approved')
                                <span class="badge bg-success"><i class="fa-solid fa-check-double me-1"></i> ACC Manager</span>
                            @elseif($sub->c1_verified_at && $sub->status_pengajuan === 'rejected')
                                <span class="badge bg-danger"><i class="fa-solid fa-xmark me-1"></i> Ditolak Manager</span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i> Menunggu ACC</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('admin.applicants.show', ['applicant' => $sub->id]) }}" class="btn btn-sm btn-light border text-primary" title="Detail Analysis"><i class="fa-solid fa-eye"></i></a>
                                <a href="{{ route('admin.applicants.edit', ['applicant' => $sub->id]) }}" class="btn btn-sm btn-light border text-warning" title="Edit Data"><i class="fa-solid fa-pen"></i></a>
                                @if($sub->c1_verified_at && $sub->smartResult)
                                    <a href="{{ route('admin.history.pdf', ['submission' => $sub->id]) }}" class="btn btn-sm btn-light border text-danger" title="Download PDF"><i class="fa-solid fa-file-pdf"></i></a>
                                @endif
                                <form action="{{ route('admin.applicants.destroy', ['applicant' => $sub->id]) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-light border text-danger btn-delete" 
                                        data-name="{{ $sub->user->name ?? 'pengajuan ini' }}"
                                        data-no="{{ $sub->no_pengajuan }}"
                                        title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
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

@section('scripts')
<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
        const name = this.dataset.name;
        const no   = this.dataset.no;
        const form = this.closest('form');

        Swal.fire({
            icon: 'warning',
            title: 'Hapus Data Pengajuan?',
            html: `Data pengajuan <strong>${no}</strong> atas nama <strong>${name}</strong> akan dihapus permanen.<br><br>Tindakan ini <strong>tidak dapat dibatalkan</strong>.`,
            showCancelButton: true,
            confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#94A3B8',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('admin.applicants.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
      @csrf
      <div class="modal-header border-0 pb-0">
        <h1 class="modal-title fs-5 fw-bold"><i class="fa-solid fa-file-import text-info me-2"></i> Import Data Excel/CSV</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning small">
            Pastikan Anda telah mendownload <b>Template Excel</b>, mengisi datanya dengan format yang benar, lalu menyimpannya dalam format <b>.CSV (Comma delimited)</b> sebelum meng-uploadnya ke sini.
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Pilih File (.csv)</label>
            <input class="form-control" type="file" name="file" accept=".csv" required>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-info text-white rounded-3 px-4"><i class="fa-solid fa-upload me-1"></i> Upload & Import</button>
      </div>
    </form>
  </div>
</div>

@endsection
