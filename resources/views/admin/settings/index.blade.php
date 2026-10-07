@extends('layouts.app')

@section('title', 'Pengaturan Sistem & Threshold SMART')
@section('page-title', 'Pengaturan Ambang Batas SMART & Informasi Bank')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card-custom p-4">
            <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom"><i class="fa-solid fa-gears text-primary me-2"></i> Pengaturan Threshold SMART & System</h5>

            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf

                <div class="mb-4 p-3 bg-light rounded-3 border">
                    <label class="form-label fw-bold text-primary mb-1">Ambang Batas Nilai SMART (Threshold Decision)</label>
                    <div class="text-muted small mb-2">Nasabah dengan Skor SMART ≥ Threshold akan direkomendasikan DITERIMA, sebaliknya < Threshold akan TIDAK DITERIMA.</div>
                    <div class="input-group">
                        <span class="input-group-text bg-white fw-bold"><i class="fa-solid fa-gauge text-warning"></i></span>
                        <input type="number" step="0.1" min="0" max="100" name="smart_threshold" value="{{ old('smart_threshold', $smartThreshold) }}" class="form-control form-control-lg fw-bold text-primary" required>
                        <span class="input-group-text bg-white">/ 100</span>
                    </div>
                </div>

                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold text-dark"><i class="fa-solid fa-robot text-primary me-1"></i> Perhitungan Otomatis SMART</label>
                    <div class="text-muted small mb-3">Jika Aktif, saat admin melakukan Verifikasi SLIK, mesin akan langsung menghitung hasil kelayakan. Jika Nonaktif, admin harus menekan tombol "Hitung Ulang" secara manual di menu Engine Transparansi.</div>
                    <div class="form-check form-switch fs-5">
                        <input class="form-check-input" type="checkbox" role="switch" id="auto_calc" name="smart_auto_calculate" value="1" {{ old('smart_auto_calculate', $smartAutoCalc) == '1' ? 'checked' : '' }}>
                        <label class="form-check-label fs-6 ms-2 mt-1" for="auto_calc">Aktifkan Perhitungan Otomatis</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Bank / Lembaga Keuangan</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $bankName) }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat Kantor Pusat</label>
                    <textarea name="bank_address" class="form-control" rows="2" required>{{ old('bank_address', $bankAddress) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Telepon Call Center</label>
                        <input type="text" name="bank_phone" value="{{ old('bank_phone', $bankPhone) }}" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">WhatsApp Admin (Untuk Live Chat)</label>
                        <input type="text" name="admin_wa" value="{{ old('admin_wa', $adminWa) }}" class="form-control" placeholder="Contoh: 6281234567890" required>
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-signature text-primary me-2"></i> Pengaturan Tanda Tangan Laporan PDF</h6>
                <div class="p-3 bg-light rounded-3 border mb-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jabatan Penandatangan</label>
                        <input type="text" name="jabatan_ttd" value="{{ old('jabatan_ttd', $jabatanTtd) }}" class="form-control" placeholder="Contoh: Manager Analis Kredit KPR" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap Penandatangan (Default)</label>
                        <div class="text-muted small mb-1">Nama ini akan digunakan jika dokumen belum di-approve oleh pimpinan di sistem.</div>
                        <input type="text" name="nama_ttd" value="{{ old('nama_ttd', $namaTtd) }}" class="form-control" placeholder="Contoh: Anisa Kencana, SE, MM" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">NIP / ID Karyawan</label>
                        <input type="text" name="nip_ttd" value="{{ old('nip_ttd', $nipTtd) }}" class="form-control" placeholder="Opsional (kosongkan jika tidak ada)">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 shadow"><i class="fa-solid fa-save me-1"></i> Simpan Pengaturan System</button>

            </form>
        </div>
    </div>
</div>
@endsection
