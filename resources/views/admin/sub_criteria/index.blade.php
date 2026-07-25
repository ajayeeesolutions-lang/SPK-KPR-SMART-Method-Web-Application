@extends('layouts.app')

@section('title', 'Sub Kriteria & Utility SMART')
@section('page-title', 'Kelola Sub Kriteria & Nilai Utility')

@section('content')
<div class="row g-3 mb-4">
    <!-- Criterion Selector Cards -->
    <div class="col-12">
        <div class="card-custom p-3">
            <div class="d-flex align-items-center gap-2 overflow-auto py-1">
                <span class="fw-bold text-muted me-2 text-nowrap">Pilih Kriteria:</span>
                @foreach($criteria as $c)
                    <a href="{{ route('admin.sub-criteria.index', ['criterion_id' => $c->id]) }}"
                       class="btn btn-sm rounded-pill text-nowrap {{ ($selectedCriterion && $selectedCriterion->id == $c->id) ? 'btn-primary shadow-sm' : 'btn-light border text-secondary' }}">
                        <strong class="me-1">{{ $c->code }}</strong> - {{ $c->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

@if($selectedCriterion)
<div class="row g-3">
    <!-- Sub Criteria List -->
    <div class="col-12 col-lg-8">
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Sub Kriteria: <span class="text-primary">{{ $selectedCriterion->code }} - {{ $selectedCriterion->name }}</span></h5>
                    <div class="text-muted small">Tipe: <span class="badge bg-light text-dark border text-uppercase">{{ $selectedCriterion->type }}</span> | Bobot: {{ number_format($selectedCriterion->weight, 2) }}%</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>No</th>
                            <th>Nama Sub Kriteria</th>
                            <th>Operator Rule</th>
                            <th>Nilai Parameter</th>
                            <th>Nilai Utility ($u_i$)</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($selectedCriterion->subCriteria as $index => $sub)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><div class="fw-bold text-dark">{{ $sub->name }}</div></td>
                                <td><span class="badge bg-light text-primary border font-monospace fs-6">{{ $sub->operator }}</span></td>
                                <td>
                                    @if($sub->operator === 'equals_text')
                                        <span class="fw-semibold text-secondary">Text: "{{ $sub->text_value }}"</span>
                                    @elseif($sub->operator === 'between')
                                        <span class="fw-semibold text-secondary">{{ number_format($sub->min_val) }} s/d {{ number_format($sub->max_val) }}</span>
                                    @else
                                        <span class="fw-semibold text-secondary">{{ $sub->operator }} {{ number_format($sub->min_val) }}</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-success fs-6 fw-bold">{{ number_format($sub->utility_value, 0) }}</span></td>
                                <td class="text-center">
                                    <form action="{{ route('admin.sub-criteria.destroy', $sub->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus sub kriteria ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada sub kriteria untuk {{ $selectedCriterion->code }}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Sub Criteria Form -->
    <div class="col-12 col-lg-4">
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-plus-circle text-primary me-2"></i> Tambah Sub Kriteria</h6>
            <form action="{{ route('admin.sub-criteria.store') }}" method="POST">
                @csrf
                <input type="hidden" name="criterion_id" value="{{ $selectedCriterion->id }}">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Sub Kriteria</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: > 10 Juta atau Tetap" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Operator Rule</label>
                    <select name="operator" class="form-select" id="opSelect" required>
                        <option value=">">Lebih besar ( > )</option>
                        <option value=">=">Lebih besar sama dengan ( >= )</option>
                        <option value="<">Lebih kecil ( < )</option>
                        <option value="<=">Lebih kecil sama dengan ( <= )</option>
                        <option value="between">Rentang Antara ( Between )</option>
                        <option value="equals_text">Sama dengan Teks ( Exact Text )</option>
                    </select>
                </div>

                <div class="mb-3" id="minValGroup">
                    <label class="form-label fw-semibold">Nilai Parameter Min / Utama</label>
                    <input type="number" step="any" name="min_val" class="form-control" placeholder="Nilai angka...">
                </div>

                <div class="mb-3" id="maxValGroup" style="display: none;">
                    <label class="form-label fw-semibold">Nilai Parameter Max (Untuk Between)</label>
                    <input type="number" step="any" name="max_val" class="form-control" placeholder="Nilai batas atas...">
                </div>

                <div class="mb-3" id="textValGroup" style="display: none;">
                    <label class="form-label fw-semibold">Teks Cocok (Exact Text)</label>
                    <input type="text" name="text_value" class="form-control" placeholder="Misal: PNS/BUMN atau Lancar">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nilai Utility ($u_i$) [0 - 100]</label>
                    <input type="number" step="0.1" name="utility_value" class="form-control" placeholder="100" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 shadow-sm"><i class="fa-solid fa-save me-1"></i> Simpan Sub Kriteria</button>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    $('#opSelect').on('change', function() {
        const val = $(this).val();
        if (val === 'between') {
            $('#minValGroup').show();
            $('#maxValGroup').show();
            $('#textValGroup').hide();
        } else if (val === 'equals_text') {
            $('#minValGroup').hide();
            $('#maxValGroup').hide();
            $('#textValGroup').show();
        } else {
            $('#minValGroup').show();
            $('#maxValGroup').hide();
            $('#textValGroup').hide();
        }
    });
</script>
@endsection
