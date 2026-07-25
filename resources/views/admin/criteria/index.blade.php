@extends('layouts.app')

@section('title', 'Master Kriteria & Bobot SMART')
@section('page-title', 'Kelola Kriteria & Bobot SMART')

@section('content')
<div class="row g-3">
    <!-- Criteria List Table -->
    <div class="col-12 col-lg-8">
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Daftar Kriteria SMART</h5>
                    <div class="text-muted small">Total bobot kriteria saat ini: <strong class="{{ $totalWeight == 100 ? 'text-success' : 'text-danger' }}">{{ number_format($totalWeight, 2) }}%</strong></div>
                </div>

                @if($totalWeight != 100)
                    <div class="badge bg-danger p-2" title="Total bobot idealnya adalah 100%">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Total Bobot ≠ 100%
                    </div>
                @else
                    <div class="badge bg-success p-2">
                        <i class="fa-solid fa-circle-check me-1"></i> Total Bobot 100% Valid
                    </div>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Kriteria</th>
                            <th>Tipe</th>
                            <th>Bobot Awal ($W$)</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($criteria as $crit)
                            <tr>
                                <td><span class="badge bg-primary fs-6 fw-bold">{{ $crit->code }}</span></td>
                                <td><div class="fw-bold text-dark">{{ $crit->name }}</div></td>
                                <td>
                                    @if($crit->type === 'benefit')
                                        <span class="badge bg-success bg-opacity-10 text-success fw-bold text-uppercase">Benefit</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning fw-bold text-uppercase">Cost</span>
                                    @endif
                                </td>
                                <td><span class="fw-bold fs-6">{{ number_format($crit->weight, 2) }}%</span></td>
                                <td>
                                    @if($crit->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-light border text-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $crit->id }}"><i class="fa-solid fa-pen"></i></button>
                                    <form action="{{ route('admin.criteria.destroy', $crit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kriteria ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $crit->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.criteria.update', $crit->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit Kriteria {{ $crit->code }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Nama Kriteria</label>
                                                    <input type="text" name="name" value="{{ $crit->name }}" class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Tipe Kriteria</label>
                                                    <select name="type" class="form-select" required>
                                                        <option value="benefit" {{ $crit->type === 'benefit' ? 'selected' : '' }}>Benefit (Semakin besar nilai, semakin baik)</option>
                                                        <option value="cost" {{ $crit->type === 'cost' ? 'selected' : '' }}>Cost (Semakin kecil nilai/rasio, semakin baik)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Bobot (%)</label>
                                                    <input type="number" step="0.01" name="weight" value="{{ $crit->weight }}" class="form-control" required>
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="active{{ $crit->id }}" {{ $crit->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="active{{ $crit->id }}">Aktifkan Kriteria dalam Perhitungan SMART</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add New Criteria Form -->
    <div class="col-12 col-lg-4">
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-plus-circle text-primary me-2"></i> Tambah Kriteria Baru</h6>
            <form action="{{ route('admin.criteria.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode Kriteria</label>
                    <input type="text" name="code" class="form-control" placeholder="Contoh: C6" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Kriteria</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Kriteria..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tipe Kriteria</label>
                    <select name="type" class="form-select" required>
                        <option value="benefit">Benefit</option>
                        <option value="cost">Cost</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Bobot (%)</label>
                    <input type="number" step="0.01" name="weight" class="form-control" placeholder="10.00" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 shadow-sm"><i class="fa-solid fa-save me-1"></i> Simpan Kriteria</button>
            </form>
        </div>
    </div>
</div>
@endsection
