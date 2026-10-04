@extends('layouts.app')

@section('title', 'Review Manager - ' . $submission->no_pengajuan)
@section('page-title', 'Review & Persetujuan Akhir Manager KPR')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card-custom p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-user text-primary me-2"></i> Profil & Finansial Nasabah</h5>
            <div class="row g-3 small">
                <div class="col-6 col-md-4">
                    <span class="text-muted d-block">Nama Nasabah</span>
                    <strong class="text-dark fs-6">{{ $submission->user->name }}</strong>
                </div>
                <div class="col-6 col-md-4">
                    <span class="text-muted d-block">NIK</span>
                    <strong class="text-dark">{{ $submission->user->profile->nik ?? '-' }}</strong>
                </div>
                <div class="col-6 col-md-4">
                    <span class="text-muted d-block">Pekerjaan</span>
                    <strong class="text-dark">{{ $submission->user->profile->pekerjaan ?? '-' }}</strong>
                </div>
                <div class="col-6 col-md-4">
                    <span class="text-muted d-block">Total Penghasilan</span>
                    <strong class="text-success">Rp {{ number_format(($submission->user->profile->penghasilan_bulanan ?? 0) + ($submission->user->profile->penghasilan_pasangan ?? 0), 0, ',', '.') }}</strong>
                </div>
                <div class="col-6 col-md-4">
                    <span class="text-muted d-block">Plafon KPR</span>
                    <strong class="text-primary">Rp {{ number_format($submission->nilai_pinjaman, 0, ',', '.') }}</strong>
                </div>
                <div class="col-6 col-md-4">
                    <span class="text-muted d-block">Tenor</span>
                    <strong class="text-dark">{{ $submission->tenor_tahun }} Tahun</strong>
                </div>
            </div>
        </div>

        <!-- SMART Engine Matrix -->
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-table text-primary me-2"></i> Hasil Rekomendasi SMART System</h6>
            <div class="p-3 mb-3 bg-light rounded-3 border">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted fw-bold">SKOR SMART SISTEM</div>
                        <span class="display-6 fw-bold text-primary">{{ number_format($submission->final_smart_score ?? 0, 2) }}</span>
                        <small class="text-muted">/ 100.00</small>
                    </div>
                    <div>
                        @if($submission->status_keputusan === 'LAYAK')
                            <span class="badge-success-custom fs-5"><i class="fa-solid fa-circle-check me-1"></i> LAYAK</span>
                        @elseif($submission->status_keputusan === 'DIPERTIMBANGKAN')
                            <span class="badge bg-warning text-dark fs-5"><i class="fa-solid fa-triangle-exclamation me-1"></i> DIPERTIMBANGKAN</span>
                        @else
                            <span class="badge-danger-custom fs-5"><i class="fa-solid fa-circle-xmark me-1"></i> TIDAK LAYAK</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="small">
                <strong class="d-block mb-1">Analisis Otomatis Mesin SMART:</strong>
                @if(isset($submission->smartResult->explanations['summary']))
                    <ul class="ps-3 text-muted">
                        @foreach($submission->smartResult->explanations['summary'] as $reason)
                            <li>{{ $reason }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Approval Action Card -->
    <div class="col-12 col-lg-4">
        <div class="card-custom p-4 h-100">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-stamp text-primary me-2"></i> Form Persetujuan Manager</h5>

            <div class="mb-3">
                <span class="text-muted small d-block">Status Pengajuan Saat Ini:</span>
                @if($submission->status_pengajuan === 'approved')
                    <span class="badge bg-success fs-6"><i class="fa-solid fa-check me-1"></i> Approved oleh {{ $submission->approver->name ?? 'Manager' }}</span>
                @elseif($submission->status_pengajuan === 'rejected')
                    <span class="badge bg-danger fs-6"><i class="fa-solid fa-xmark me-1"></i> Rejected oleh {{ $submission->approver->name ?? 'Manager' }}</span>
                @else
                    <span class="badge bg-warning text-dark fs-6"><i class="fa-solid fa-clock me-1"></i> Menunggu Keputusan Manager</span>
                @endif
            </div>

            {{-- Approve Form --}}
            <form action="{{ route('manager.submissions.approve', $submission->id) }}" method="POST" class="mb-3" id="approveForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Catatan Manager (Opsional untuk Setuju)</label>
                    <textarea name="manager_notes" id="approveNotes" class="form-control form-control-sm" rows="3" placeholder="Tambahkan catatan persetujuan...">{{ $submission->manager_notes }}</textarea>
                </div>
                <button type="button" onclick="confirmApprove()" class="btn btn-success w-100 py-2 rounded-3 shadow-sm">
                    <i class="fa-solid fa-check-circle me-1"></i> SETUJUI PENGAJUAN (APPROVE)
                </button>
            </form>

            <hr>

            {{-- Reject Form --}}
            <form action="{{ route('manager.submissions.reject', $submission->id) }}" method="POST" id="rejectForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-danger">Alasan Penolakan (Wajib jika menolak)</label>
                    <textarea name="manager_notes" id="rejectNotes" class="form-control form-control-sm" rows="2" placeholder="Tuliskan alasan penolakan..." required></textarea>
                </div>
                <button type="button" onclick="confirmReject()" class="btn btn-outline-danger w-100 py-2 rounded-3">
                    <i class="fa-solid fa-times-circle me-1"></i> TOLAK PENGAJUAN (REJECT)
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmApprove() {
    Swal.fire({
        icon: 'question',
        title: 'Setujui Pengajuan?',
        html: `Anda akan <strong>menyetujui</strong> pengajuan KPR <strong>{{ $submission->no_pengajuan }}</strong> atas nama <strong>{{ $submission->user->name }}</strong>.<br><br>Tindakan ini tidak dapat dibatalkan.`,
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Ya, Setujui',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#16A34A',
        cancelButtonColor: '#94A3B8',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            // Sync catatan ke form sebelum submit
            document.querySelector('#approveForm textarea[name="manager_notes"]').value = document.getElementById('approveNotes').value;
            document.getElementById('approveForm').submit();
        }
    });
}

function confirmReject() {
    const alasan = document.getElementById('rejectNotes').value.trim();
    if (!alasan) {
        Swal.fire({
            icon: 'warning',
            title: 'Alasan Wajib Diisi',
            text: 'Mohon tuliskan alasan penolakan sebelum menolak pengajuan.',
            confirmButtonColor: '#2563EB',
        });
        document.getElementById('rejectNotes').focus();
        return;
    }

    Swal.fire({
        icon: 'warning',
        title: 'Tolak Pengajuan?',
        html: `Anda akan <strong>menolak</strong> pengajuan KPR <strong>{{ $submission->no_pengajuan }}</strong> atas nama <strong>{{ $submission->user->name }}</strong>.<br><br><em>"${alasan}"</em>`,
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-xmark me-1"></i> Ya, Tolak',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#94A3B8',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('rejectForm').submit();
        }
    });
}
</script>
@endsection
