@extends('layouts.app')

@section('title', 'Form Pengajuan KPR Baru')
@section('page-title', 'Form Pengajuan Fasilitas KPR')

@section('content')
<div class="card-custom p-4">
    <div class="mb-4 pb-3 border-bottom">
        <h5 class="fw-bold text-dark mb-1">Ajukan KPR Baru</h5>
        <div class="text-muted small">Input nominal properti & unggah dokumen persyaratan pendukung.</div>
    </div>

    <form action="{{ route('nasabah.submission.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Harga Rumah Properti (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="harga_rumah" id="hargaRumah" value="{{ old('harga_rumah', 500000000) }}" class="form-control" required>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Uang Muka (DP) (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="uang_muka_dp" id="dpRumah" value="{{ old('uang_muka_dp', 100000000) }}" class="form-control" required>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Nilai Pinjaman KPR (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="nilai_pinjaman" id="nilaiPinjaman" value="{{ old('nilai_pinjaman', 400000000) }}" class="form-control" readonly required>
                <small class="text-muted">Otomatis dihitung (Harga Rumah - Uang Muka DP)</small>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Tenor Pinjaman (Tahun) <span class="text-danger">*</span></label>
                <select name="tenor_tahun" class="form-select" required>
                    <option value="5">5 Tahun</option>
                    <option value="10">10 Tahun</option>
                    <option value="15" selected>15 Tahun</option>
                    <option value="20">20 Tahun</option>
                    <option value="25">25 Tahun</option>
                </select>
            </div>
        </div>

        <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Upload Dokumen Persyaratan (PDF / Image)</h6>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="p-3 border rounded-3 bg-light">
                    <label class="form-label fw-semibold small">1. KTP Nasabah</label>
                    <input type="file" name="ktp" class="form-control form-control-sm doc-input" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="p-3 border rounded-3 bg-light">
                    <label class="form-label fw-semibold small">2. Kartu Keluarga (KK)</label>
                    <input type="file" name="kk" class="form-control form-control-sm doc-input" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="p-3 border rounded-3 bg-light">
                    <label class="form-label fw-semibold small">3. Slip Gaji 3 Bulan Terakhir</label>
                    <input type="file" name="slip_gaji" class="form-control form-control-sm doc-input" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="p-3 border rounded-3 bg-light">
                    <label class="form-label fw-semibold small">4. NPWP Pribadi</label>
                    <input type="file" name="npwp" class="form-control form-control-sm doc-input" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="p-3 border rounded-3 bg-light">
                    <label class="form-label fw-semibold small">5. Rekening Koran 3 Bulan</label>
                    <input type="file" name="rekening_koran" class="form-control form-control-sm doc-input" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="p-3 border rounded-3 bg-light">
                    <label class="form-label fw-semibold small">6. Surat Ket. Kerja / SK</label>
                    <input type="file" name="sk_kerja" class="form-control form-control-sm doc-input" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>
        </div>

        <div class="text-end pt-3 border-top">
            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow"><i class="fa-solid fa-paper-plane me-1"></i> Kirim Pengajuan & Process SMART Engine</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function calculateLoan() {
        const harga = parseFloat($('#hargaRumah').val()) || 0;
        const dp = parseFloat($('#dpRumah').val()) || 0;
        const pinjaman = Math.max(0, harga - dp);
        $('#nilaiPinjaman').val(pinjaman);
    }

    $('#hargaRumah, #dpRumah').on('input', calculateLoan);
</script>
@endsection
