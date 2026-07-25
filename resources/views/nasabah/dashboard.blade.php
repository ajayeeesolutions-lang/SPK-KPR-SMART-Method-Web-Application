@extends('layouts.app')

@section('title', 'Dashboard Calon Nasabah')
@section('page-title', 'Selamat Datang, ' . auth()->user()->name)

@section('content')
<!-- Profile Notice Alert -->
@if(!$profile)
    <div class="alert alert-warning border-0 shadow-sm p-4 rounded-4 mb-4">
        <div class="d-flex align-items-center gap-3">
            <i class="fa-solid fa-id-card fs-1 text-warning"></i>
            <div>
                <h5 class="fw-bold mb-1">Profil Biodata Belum Lengkap!</h5>
                <div class="small text-muted">Silakan lengkapi profil biodata dan informasi pekerjaan/finansial Anda sebelum mengajukan KPR.</div>
            </div>
            <a href="{{ route('nasabah.profile.edit') }}" class="btn btn-warning ms-auto rounded-3 fw-bold px-3">Lengkapi Profil Sekarang</a>
        </div>
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-12 col-md-8">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-file-invoice text-primary me-2"></i> Status Pengajuan KPR Anda</h5>
                @if($profile)
                    <a href="{{ route('nasabah.submission.create') }}" class="btn btn-primary btn-sm rounded-pill"><i class="fa-solid fa-plus me-1"></i> Pengajuan Baru</a>
                @endif
            </div>

            @if($activeSubmission)
                <div class="p-3 bg-light rounded-4 border mb-4">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-7">
                            <span class="badge bg-primary text-uppercase mb-2">{{ $activeSubmission->no_pengajuan }}</span>
                            <h5 class="fw-bold text-dark mb-1">Plafon: Rp {{ number_format($activeSubmission->nilai_pinjaman, 0, ',', '.') }}</h5>
                            <small class="text-muted d-block">Harga Rumah: Rp {{ number_format($activeSubmission->harga_rumah, 0, ',', '.') }} | Tenor: {{ $activeSubmission->tenor_tahun }} Thn</small>
                        </div>
                        <div class="col-12 col-md-5 text-md-end mt-3 mt-md-0">
                            @if($activeSubmission->status_keputusan === 'DITERIMA')
                                <span class="badge-success-custom fs-5 d-inline-block"><i class="fa-solid fa-circle-check me-1"></i> DITERIMA</span>
                            @elseif($activeSubmission->status_keputusan === 'TIDAK DITERIMA')
                                <span class="badge-danger-custom fs-5 d-inline-block"><i class="fa-solid fa-circle-xmark me-1"></i> TIDAK DITERIMA</span>
                            @else
                                <span class="badge-warning-custom fs-6 d-inline-block"><i class="fa-solid fa-clock me-1"></i> Dalam Process Evaluation</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Timeline Progress -->
                <h6 class="fw-bold text-dark mb-3">Timeline Progres Pengajuan:</h6>
                <div class="d-flex justify-content-between text-center small position-relative mb-4">
                    <div class="w-25">
                        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-1" style="width: 32px; height: 32px;"><i class="fa-solid fa-check"></i></div>
                        <div class="fw-bold text-dark">Submit</div>
                    </div>
                    <div class="w-25">
                        <div class="rounded-circle {{ $activeSubmission->smartResult ? 'bg-success text-white' : 'bg-secondary text-white' }} d-inline-flex align-items-center justify-content-center mb-1" style="width: 32px; height: 32px;"><i class="fa-solid fa-microchip"></i></div>
                        <div class="fw-bold text-dark">SMART Engine</div>
                    </div>
                    <div class="w-25">
                        <div class="rounded-circle {{ $activeSubmission->approved_at ? 'bg-success text-white' : 'bg-secondary text-white' }} d-inline-flex align-items-center justify-content-center mb-1" style="width: 32px; height: 32px;"><i class="fa-solid fa-user-check"></i></div>
                        <div class="fw-bold text-dark">Verifikasi Manager</div>
                    </div>
                    <div class="w-25">
                        <div class="rounded-circle {{ $activeSubmission->status_keputusan ? 'bg-primary text-white' : 'bg-secondary text-white' }} d-inline-flex align-items-center justify-content-center mb-1" style="width: 32px; height: 32px;"><i class="fa-solid fa-award"></i></div>
                        <div class="fw-bold text-dark">Hasil Akhir</div>
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('nasabah.submission.show', $activeSubmission->id) }}" class="btn btn-outline-primary rounded-3"><i class="fa-solid fa-circle-info me-1"></i> Lihat Detail Analysis & Download Certificate</a>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-house-circle-exclamation fs-1 mb-3 d-block opacity-50"></i>
                    Anda belum memiliki pengajuan KPR aktif.
                </div>
            @endif
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card-custom p-4 h-100">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-id-card text-primary me-2"></i> Ringkasan Profil Anda</h6>

            @if($profile)
                <div class="small">
                    <div class="mb-2"><span class="text-muted d-block">NIK:</span> <strong>{{ $profile->nik }}</strong></div>
                    <div class="mb-2"><span class="text-muted d-block">Pekerjaan:</span> <strong>{{ $profile->pekerjaan }}</strong> ({{ $profile->status_pekerjaan }})</div>
                    <div class="mb-2"><span class="text-muted d-block">Penghasilan Total:</span> <strong class="text-success">Rp {{ number_format($profile->total_penghasilan, 0, ',', '.') }}</strong></div>
                    <div class="mb-3"><span class="text-muted d-block">Riwayat Kredit:</span> <strong class="text-primary">{{ $profile->riwayat_kredit }}</strong></div>

                    <a href="{{ route('nasabah.profile.edit') }}" class="btn btn-light border w-100 rounded-3"><i class="fa-solid fa-user-pen me-1"></i> Edit Profil Biodata</a>
                </div>
            @else
                <div class="small text-muted mb-3">Biodata Anda belum diisi.</div>
                <a href="{{ route('nasabah.profile.edit') }}" class="btn btn-warning w-100 rounded-3 fw-bold">Isi Profil Biodata</a>
            @endif
        </div>
    </div>
</div>
@endsection
