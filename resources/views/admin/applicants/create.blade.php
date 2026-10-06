@extends('layouts.app')

@section('title', 'Tambah Calon Nasabah KPR')
@section('page-title', 'Form Input Data Calon Nasabah KPR')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h5 class="fw-bold text-dark mb-1">Tambah Data Nasabah & Pengajuan KPR</h5>
            <div class="text-muted small">Lengkapi seluruh kolom identitas, pekerjaan, dan finansial.</div>
        </div>
        <a href="{{ route('admin.applicants.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger p-3 rounded-3 mb-4">
            <h6 class="fw-bold mb-2"><i class="fa-solid fa-triangle-exclamation me-1"></i> Terdapat kesalahan pengisian form:</h6>
            <ul class="mb-0 ps-3 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.applicants.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Tab Nav -->
        <ul class="nav nav-tabs mb-4" id="formTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold" id="pribadi-tab" data-bs-toggle="tab" data-bs-target="#pribadi-pane" type="button"><i class="fa-solid fa-user me-2"></i> Data Pribadi</button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold" id="finansial-tab" data-bs-toggle="tab" data-bs-target="#finansial-pane" type="button"><i class="fa-solid fa-wallet me-2"></i> Finansial & Pekerjaan</button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold" id="kpr-tab" data-bs-toggle="tab" data-bs-target="#kpr-pane" type="button"><i class="fa-solid fa-house me-2"></i> Data Pengajuan KPR</button>
            </li>
        </ul>

        <div class="tab-content" id="formTabsContent">
            <!-- Pane 1: Data Pribadi -->
            <div class="tab-pane fade show active" id="pribadi-pane">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Nama Lengkap (Sesuai KTP) <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Nama Lengkap" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="email@domain.com" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">NIK (Nomor Induk Kependudukan) <span class="text-danger">*</span></label>
                        <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" class="form-control" placeholder="16 Digit NIK" required>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="form-control" required>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="form-control" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="Laki-laki" {{ old('jenis_kelamin') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Nomor Handphone / WA <span class="text-danger">*</span></label>
                        <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="form-control" placeholder="081234567890" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Status Pernikahan <span class="text-danger">*</span></label>
                        <select name="status_pernikahan" class="form-select" required>
                            <option value="Belum Menikah">Belum Menikah</option>
                            <option value="Menikah">Menikah</option>
                            <option value="Cerai">Cerai</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Jumlah Tanggungan <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_tanggungan" value="{{ old('jumlah_tanggungan', 0) }}" class="form-control" min="0" required>
                    </div>

                    <div class="col-12 col-md-8">
                        <label class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="alamat" value="{{ old('alamat') }}" class="form-control" placeholder="Jl. Nama Jalan No. XX..." required>
                    </div>
                </div>
            </div>

            <!-- Pane 2: Finansial -->
            <div class="tab-pane fade" id="finansial-pane">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Pekerjaan <span class="text-danger">*</span></label>
                        <input type="text" name="pekerjaan" value="{{ old('pekerjaan') }}" class="form-control" placeholder="Jabatan / Profesi" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Status & Lama Pekerjaan (Kriteria C3) <span class="text-danger">*</span></label>
                        <select name="status_pekerjaan" class="form-select" required>
                            <option value="Pegawai Tetap > 2 Tahun">Pegawai Tetap > 2 Tahun</option>
                            <option value="Pegawai Tetap < 2 Tahun">Pegawai Tetap < 2 Tahun</option>
                            <option value="Pegawai Kontrak > 2 Tahun">Pegawai Kontrak > 2 Tahun</option>
                            <option value="Pegawai Kontrak < 2 Tahun">Pegawai Kontrak < 2 Tahun</option>
                            <option value="Wiraswasta / Lainnya">Wiraswasta / Lainnya</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Lama Bekerja (Bulan) (Kriteria C2) <span class="text-danger">*</span></label>
                        <input type="number" name="lama_bekerja_bulan" value="{{ old('lama_bekerja_bulan', 36) }}" class="form-control" placeholder="Contoh: 36 (3 tahun)" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Penghasilan Bulanan (Rp) (Kriteria C2) <span class="text-danger">*</span></label>
                        <input type="number" name="penghasilan_bulanan" value="{{ old('penghasilan_bulanan', 10000000) }}" class="form-control" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Penghasilan Pasangan (Rp)</label>
                        <input type="number" name="penghasilan_pasangan" value="{{ old('penghasilan_pasangan', 0) }}" class="form-control">
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Pengeluaran Bulanan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="pengeluaran_bulanan" value="{{ old('pengeluaran_bulanan', 4000000) }}" class="form-control" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Cicilan Lain saat ini (Rp) (DTI C3)</label>
                        <input type="number" name="cicilan_lain" value="{{ old('cicilan_lain', 1500000) }}" class="form-control">
                    </div>

                </div>
            </div>

            <!-- Pane 3: KPR Submission -->
            <div class="tab-pane fade" id="kpr-pane">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Harga Rumah yang Dibeli (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="harga_rumah" value="{{ old('harga_rumah', 500000000) }}" class="form-control" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Uang Muka (DP) (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="uang_muka_dp" value="{{ old('uang_muka_dp', 100000000) }}" class="form-control" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Plafon Pinjaman KPR (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="nilai_pinjaman" value="{{ old('nilai_pinjaman', 400000000) }}" class="form-control" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Tenor Pinjaman (Tahun) <span class="text-danger">*</span></label>
                        <input type="number" name="tenor_tahun" value="{{ old('tenor_tahun', 15) }}" min="1" max="30" class="form-control" required>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow">
                        <i class="fa-solid fa-calculator me-2"></i> Simpan Data & Jalankan SMART Engine
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
