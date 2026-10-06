@extends('layouts.app')

@section('title', 'Edit Data Pengajuan KPR')
@section('page-title', 'Edit Data Pengajuan KPR')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h5 class="fw-bold text-dark mb-1">Edit Pengajuan {{ $submission->no_pengajuan }}</h5>
            <div class="text-muted small">Ubah nilai pengajuan atau atribut kriteria nasabah.</div>
        </div>
        <a href="{{ route('admin.applicants.show', ['applicant' => $submission->id]) }}" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="fa-solid fa-arrow-left me-1"></i> Batal</a>
    </div>

    <form action="{{ route('admin.applicants.update', ['applicant' => $submission->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Nama Nasabah</label>
                <input type="text" name="name" value="{{ old('name', $submission->user?->name) }}" class="form-control" required>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Harga Rumah (Rp)</label>
                <input type="number" name="harga_rumah" value="{{ old('harga_rumah', $submission->harga_rumah) }}" class="form-control" required>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Uang Muka DP (Rp)</label>
                <input type="number" name="uang_muka_dp" value="{{ old('uang_muka_dp', $submission->uang_muka_dp) }}" class="form-control" required>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Plafon Pinjaman (Rp)</label>
                <input type="number" name="nilai_pinjaman" value="{{ old('nilai_pinjaman', $submission->nilai_pinjaman) }}" class="form-control" required>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Tenor (Tahun)</label>
                <input type="number" name="tenor_tahun" value="{{ old('tenor_tahun', $submission->tenor_tahun) }}" class="form-control" required>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Penghasilan Bulanan (Rp) (C2)</label>
                <input type="number" name="penghasilan_bulanan" value="{{ old('penghasilan_bulanan', $submission->user?->profile?->penghasilan_bulanan ?? 0) }}" class="form-control" required>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Lama Bekerja (Bulan)</label>
                <input type="number" name="lama_bekerja_bulan" value="{{ old('lama_bekerja_bulan', $submission->user?->profile?->lama_bekerja_bulan ?? 0) }}" class="form-control" required>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Status & Lama Pekerjaan (C3)</label>
                <select name="status_pekerjaan" class="form-select" required>
                    @foreach(['Pegawai Tetap > 2 Tahun', 'Pegawai Tetap < 2 Tahun', 'Pegawai Kontrak > 2 Tahun', 'Pegawai Kontrak < 2 Tahun', 'Wiraswasta / Lainnya'] as $st)
                        <option value="{{ $st }}" {{ ($submission->user?->profile?->status_pekerjaan ?? '') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="mt-4 pt-3 border-top text-end">
            <button type="submit" class="btn btn-primary px-4 rounded-3 shadow"><i class="fa-solid fa-save me-1"></i> Simpan & Recalculate SMART</button>
        </div>
    </form>
</div>
@endsection
