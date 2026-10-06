@extends('layouts.app')

@section('title', 'Form Pengajuan KPR Baru')
@section('page-title', 'Form Pengajuan Fasilitas KPR')

@section('content')

{{-- STEP INDICATOR --}}
<div class="card-custom p-3 mb-4">
    <div class="d-flex align-items-center gap-0">
        <div class="d-flex align-items-center flex-fill">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:34px;height:34px;min-width:34px;">1</div>
            <div class="fw-semibold text-primary ms-2 small">Isi Profil Biodata</div>
            <div class="flex-fill border-top border-primary border-2 mx-3"></div>
        </div>
        <div class="d-flex align-items-center flex-fill">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:34px;height:34px;min-width:34px;">2</div>
            <div class="fw-semibold text-primary ms-2 small">Input Kriteria SMART</div>
            <div class="flex-fill border-top border-primary border-2 mx-3"></div>
        </div>
        <div class="d-flex align-items-center">
            <div class="border border-2 text-muted rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:34px;height:34px;min-width:34px;">3</div>
            <div class="fw-semibold text-muted ms-2 small">Submit & Hitung SMART</div>
        </div>
    </div>
</div>

<form action="{{ route('nasabah.submission.store') }}" method="POST" enctype="multipart/form-data">
@csrf

{{-- BAGIAN 1: DATA KRITERIA SMART (5 Kriteria Sesuai Naskah) --}}
<div class="card-custom p-4 mb-4">
    <div class="mb-4 pb-3 border-bottom">
        <h5 class="fw-bold text-dark mb-1"><i class="fa-solid fa-sliders text-primary me-2"></i> Bagian 1: Input Data Kriteria SMART</h5>
        <p class="text-muted small mb-0">Isi data yang Anda ketahui. Kredibilitas SLIK (C1) akan diperiksa dan dikonfirmasi admin sebelum analisis SMART dijalankan.</p>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger small rounded-3 mb-3">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row g-3">
        {{-- C2: Penghasilan Bersih --}}
        <div class="col-12 col-md-6">
            <div class="p-3 rounded-3 border h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold small" style="width:28px;height:28px;min-width:28px;">C2</div>
                    <label class="form-label fw-semibold mb-0">Penghasilan Bersih/Bulan (Rp) <span class="text-danger">*</span></label>
                </div>
                @php
                    $totalPenghasilan = ($profile->penghasilan_bulanan ?? 0) + ($profile->penghasilan_pasangan ?? 0);
                    $totalPengeluaran = ($profile->pengeluaran_bulanan ?? 0) + ($profile->cicilan_lain ?? 0);
                    $penghasilanBersih = $totalPenghasilan - $totalPengeluaran;
                @endphp
                <div class="text-muted small mb-2">Total penghasilan dikurangi pengeluaran rutin per bulan (Rp).</div>
                <input type="number" name="c2_penghasilan_bersih"
                    value="{{ old('c2_penghasilan_bersih', max(0, $penghasilanBersih)) }}"
                    class="form-control @error('c2_penghasilan_bersih') is-invalid @enderror"
                    placeholder="Contoh: 5000000" min="0" required>
                <div class="form-text small text-primary">Otomatis dihitung dari profil: Rp {{ number_format(max(0,$penghasilanBersih),0,',','.') }} — bisa diubah</div>
            </div>
        </div>

        {{-- C3: Status & Lama Pekerjaan --}}
        <div class="col-12">
            <div class="p-3 rounded-3 border">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold small" style="width:28px;height:28px;min-width:28px;">C3</div>
                    <label class="form-label fw-semibold mb-0">Status & Lama Pekerjaan <span class="text-danger">*</span></label>
                </div>
                <div class="text-muted small mb-3">Stabilitas pekerjaan memengaruhi kemampuan membayar cicilan jangka panjang.</div>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Status Pekerjaan</label>
                        <select name="c3_status_pekerjaan" class="form-select @error('c3_status_pekerjaan') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Pilih Status --</option>
                            <option value="PNS/BUMN" {{ old('c3_status_pekerjaan', $profile->status_pekerjaan ?? '') == 'PNS/BUMN' ? 'selected' : '' }}>PNS / BUMN</option>
                            <option value="Karyawan Tetap" {{ old('c3_status_pekerjaan', $profile->status_pekerjaan ?? '') == 'Karyawan Tetap' ? 'selected' : '' }}>Karyawan Tetap (Swasta)</option>
                            <option value="Karyawan Kontrak" {{ old('c3_status_pekerjaan', $profile->status_pekerjaan ?? '') == 'Karyawan Kontrak' ? 'selected' : '' }}>Karyawan Kontrak</option>
                            <option value="Wirausaha" {{ old('c3_status_pekerjaan', $profile->status_pekerjaan ?? '') == 'Wirausaha' ? 'selected' : '' }}>Wirausaha / Pengusaha</option>
                            <option value="Profesional" {{ old('c3_status_pekerjaan', $profile->status_pekerjaan ?? '') == 'Profesional' ? 'selected' : '' }}>Profesional (Dokter, Pengacara, dll.)</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Lama Bekerja (Bulan)</label>
                        <input type="number" name="c3_lama_bekerja_bulan"
                            value="{{ old('c3_lama_bekerja_bulan', $profile->lama_bekerja_bulan ?? 0) }}"
                            class="form-control @error('c3_lama_bekerja_bulan') is-invalid @enderror"
                            placeholder="Contoh: 36" min="0" required>
                        <div class="form-text small">Dihitung dalam bulan. 1 tahun = 12 bulan.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- C4: Usia --}}
        <div class="col-12 col-md-6">
            <div class="p-3 rounded-3 border h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="text-white rounded-circle d-flex align-items-center justify-content-center fw-bold small" style="width:28px;height:28px;min-width:28px;background:#9333EA;">C4</div>
                    <label class="form-label fw-semibold mb-0">Usia Saat Pengajuan (Tahun) <span class="text-danger">*</span></label>
                </div>
                @php
                    $usia = $profile->tanggal_lahir ? \Carbon\Carbon::parse($profile->tanggal_lahir)->age : null;
                @endphp
                <div class="text-muted small mb-2">Usia debitur pada saat mengajukan KPR ini.</div>
                <input type="number" name="c4_usia"
                    value="{{ old('c4_usia', $usia) }}"
                    class="form-control @error('c4_usia') is-invalid @enderror"
                    placeholder="Contoh: 32" min="17" max="80" required>
                @if($usia)
                    <div class="form-text small text-primary">Dihitung otomatis dari tanggal lahir: {{ $usia }} tahun</div>
                @endif
            </div>
        </div>

        {{-- C5: Jumlah Tanggungan --}}
        <div class="col-12 col-md-6">
            <div class="p-3 rounded-3 border h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fw-bold small" style="width:28px;height:28px;min-width:28px;">C5</div>
                    <label class="form-label fw-semibold mb-0">Jumlah Tanggungan (Orang) <span class="text-danger">*</span></label>
                </div>
                <div class="text-muted small mb-2">Jumlah anggota keluarga yang ditanggung biaya hidupnya.</div>
                <input type="number" name="c5_jumlah_tanggungan"
                    value="{{ old('c5_jumlah_tanggungan', $profile->jumlah_tanggungan ?? 0) }}"
                    class="form-control @error('c5_jumlah_tanggungan') is-invalid @enderror"
                    placeholder="Contoh: 2" min="0" required>
            </div>
        </div>
    </div>
</div>

{{-- BAGIAN 2: DATA PROPERTI & PINJAMAN KPR --}}
<div class="card-custom p-4 mb-4">
    <div class="mb-4 pb-3 border-bottom">
        <h5 class="fw-bold text-dark mb-1"><i class="fa-solid fa-house-circle-check text-primary me-2"></i> Bagian 2: Data Properti & Pinjaman KPR</h5>
        <div class="text-muted small">Isi nominal properti yang ingin diajukan.</div>
    </div>

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

    {{-- BAGIAN 3: UPLOAD DOKUMEN --}}
    <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Bagian 3: Upload Dokumen Persyaratan (PDF / Image)</h6>
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="p-3 border rounded-3 bg-light">
                <label class="form-label fw-semibold small"><i class="fa-regular fa-id-card text-primary me-1"></i> 1. KTP Nasabah</label>
                <input type="file" name="ktp" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="p-3 border rounded-3 bg-light">
                <label class="form-label fw-semibold small"><i class="fa-solid fa-users text-primary me-1"></i> 2. Kartu Keluarga (KK)</label>
                <input type="file" name="kk" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="p-3 border rounded-3 bg-light">
                <label class="form-label fw-semibold small"><i class="fa-solid fa-money-check-dollar text-primary me-1"></i> 3. Slip Gaji 3 Bulan Terakhir</label>
                <input type="file" name="slip_gaji" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="p-3 border rounded-3 bg-light">
                <label class="form-label fw-semibold small"><i class="fa-solid fa-file-invoice text-primary me-1"></i> 4. NPWP Pribadi</label>
                <input type="file" name="npwp" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="p-3 border rounded-3 bg-light">
                <label class="form-label fw-semibold small"><i class="fa-solid fa-landmark text-primary me-1"></i> 5. Rekening Koran 3 Bulan</label>
                <input type="file" name="rekening_koran" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="p-3 border rounded-3 bg-light">
                <label class="form-label fw-semibold small"><i class="fa-solid fa-briefcase text-primary me-1"></i> 6. Surat Ket. Kerja / SK</label>
                <input type="file" name="sk_kerja" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
            </div>
        </div>
    </div>

    <div class="alert alert-warning border-0 rounded-3 small mb-4">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>
        Dengan mengirimkan formulir ini, Anda menyatakan bahwa seluruh data kriteria yang diinput adalah benar dan dapat dipertanggungjawabkan. Data ini akan langsung digunakan oleh <strong>Mesin SMART</strong> untuk proses analisis kelayakan secara otomatis.
    </div>

    <div class="d-flex justify-content-between pt-3 border-top">
        <a href="{{ route('nasabah.dashboard') }}" class="btn btn-light border rounded-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Batal
        </a>
        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow">
            <i class="fa-solid fa-calculator me-1"></i> Kirim & Hitung SMART Engine
        </button>
    </div>
</div>

</form>
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
