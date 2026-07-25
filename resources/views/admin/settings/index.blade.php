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

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Bank / Lembaga Keuangan</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $bankName) }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat Kantor Pusat</label>
                    <textarea name="bank_address" class="form-control" rows="2" required>{{ old('bank_address', $bankAddress) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Telepon Call Center</label>
                    <input type="text" name="bank_phone" value="{{ old('bank_phone', $bankPhone) }}" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 shadow"><i class="fa-solid fa-save me-1"></i> Simpan Pengaturan System</button>
            </form>
        </div>
    </div>
</div>
@endsection
