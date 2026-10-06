@extends('layouts.app')

@section('title', 'Lengkapi Profil Nasabah')
@section('page-title', 'Lengkapi Profil Biodata & Finansial')

@section('content')
<div class="card-custom p-4">
    <div class="mb-4 pb-3 border-bottom">
        <h5 class="fw-bold text-dark mb-1">Form Data Diri & Finansial Calon Nasabah</h5>
        <div class="text-muted small">Informasi profil digunakan untuk penilaian. Kredibilitas SLIK akan diperiksa dan dikonfirmasi oleh admin.</div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger p-3 rounded-3 mb-4">
            <ul class="mb-0 ps-3 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('nasabah.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4 d-flex align-items-center gap-3">
            <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center overflow-hidden" style="width: 80px; height: 80px;">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <i class="fa-solid fa-user text-secondary fs-1"></i>
                @endif
            </div>
            <div>
                <label class="form-label fw-semibold">Foto Profil (Opsional)</label>
                <input type="file" name="foto" class="form-control form-control-sm" accept="image/*">
                <div class="form-text small">Upload foto diri untuk melengkapi identitas. Maks 2MB.</div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Nama Lengkap (Sesuai KTP) <span class="text-danger">*</span></label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $profile->nama_lengkap ?? $user->name) }}" class="form-control" required>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">NIK (Nomor Induk Kependudukan) <span class="text-danger">*</span></label>
                <input type="text" name="nik" value="{{ old('nik', $profile->nik ?? '') }}" maxlength="16" class="form-control" placeholder="16 Digit NIK" required>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Tempat Lahir <span class="text-danger">*</span></label>
                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $profile->tempat_lahir ?? '') }}" class="form-control" required>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($profile->tanggal_lahir)->format('Y-m-d')) }}" class="form-control" required>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="jenis_kelamin" class="form-select" required>
                    <option value="Laki-laki" {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Nomor Handphone <span class="text-danger">*</span></label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $profile->no_hp ?? $user->phone) }}" class="form-control" required>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Status Pernikahan <span class="text-danger">*</span></label>
                <select name="status_pernikahan" class="form-select" required>
                    <option value="Belum Menikah" {{ old('status_pernikahan', $profile->status_pernikahan ?? '') === 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                    <option value="Menikah" {{ old('status_pernikahan', $profile->status_pernikahan ?? '') === 'Menikah' ? 'selected' : '' }}>Menikah</option>
                    <option value="Cerai" {{ old('status_pernikahan', $profile->status_pernikahan ?? '') === 'Cerai' ? 'selected' : '' }}>Cerai</option>
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Jumlah Tanggungan <span class="text-danger">*</span></label>
                <input type="number" name="jumlah_tanggungan" value="{{ old('jumlah_tanggungan', $profile->jumlah_tanggungan ?? 0) }}" min="0" class="form-control" required>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Alamat Lengkap KTP <span class="text-danger">*</span></label>
                <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat', $profile->alamat ?? '') }}</textarea>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-briefcase me-2"></i> Informasi Pekerjaan & Finansial</h6>

            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Pekerjaan Saat Ini <span class="text-danger">*</span></label>
                <input type="text" name="pekerjaan" value="{{ old('pekerjaan', $profile->pekerjaan ?? '') }}" class="form-control" placeholder="Profesi / Jabatan" required>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Status Pekerjaan <span class="text-danger">*</span></label>
                <select name="status_pekerjaan" class="form-select" required>
                    @foreach(['PNS/BUMN', 'Pegawai Tetap Swasta', 'Wirausaha', 'Pegawai Kontrak', 'Lainnya'] as $st)
                        <option value="{{ $st }}" {{ old('status_pekerjaan', $profile->status_pekerjaan ?? '') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Lama Bekerja (Dalam Bulan) <span class="text-danger">*</span></label>
                <input type="number" name="lama_bekerja_bulan" value="{{ old('lama_bekerja_bulan', $profile->lama_bekerja_bulan ?? 24) }}" min="0" class="form-control" placeholder="Contoh: 36 (3 thn)" required>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Penghasilan Bulanan Anda (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="penghasilan_bulanan" value="{{ old('penghasilan_bulanan', $profile->penghasilan_bulanan ?? 8000000) }}" class="form-control" required>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Penghasilan Pasangan (Jika ada)</label>
                <input type="number" name="penghasilan_pasangan" value="{{ old('penghasilan_pasangan', $profile->penghasilan_pasangan ?? 0) }}" class="form-control">
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Pengeluaran Rata-rata/Bln (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="pengeluaran_bulanan" value="{{ old('pengeluaran_bulanan', $profile->pengeluaran_bulanan ?? 3500000) }}" class="form-control" required>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Total Cicilan Lain/Bln Saat Ini (Rp)</label>
                <input type="number" name="cicilan_lain" value="{{ old('cicilan_lain', $profile->cicilan_lain ?? 1000000) }}" class="form-control">
            </div>

        </div>

        <div class="mt-4 pt-3 border-top text-end">
            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow"><i class="fa-solid fa-save me-1"></i> Simpan Profil Nasabah</button>
        </div>
    </form>
</div>
@endsection
