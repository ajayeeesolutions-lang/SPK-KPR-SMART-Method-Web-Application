@extends('layouts.app')

@section('title', 'Admin Enterprise Dashboard')
@section('page-title', 'Dashboard System & Summary Analyst')

@section('content')
<!-- Metric Cards Grid -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-4 col-xl">
        <div class="card-custom p-3 d-flex align-items-center flex-row gap-3">
            <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                <i class="fa-solid fa-file-invoice fs-3"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Total Pengajuan</div>
                <h3 class="fw-bold mb-0 text-dark">{{ $totalPengajuan }}</h3>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-4 col-xl">
        <div class="card-custom p-3 d-flex align-items-center flex-row gap-3">
            <div class="rounded-3 bg-success bg-opacity-10 text-success p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                <i class="fa-solid fa-circle-check fs-3"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Layak</div>
                <h3 class="fw-bold mb-0 text-success">{{ $totalDiterima }}</h3>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-4 col-xl">
        <div class="card-custom p-3 d-flex align-items-center flex-row gap-3">
            <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                <i class="fa-solid fa-triangle-exclamation fs-3"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Dipertimbangkan</div>
                <h3 class="fw-bold mb-0 text-warning">{{ $totalDipertimbangkan }}</h3>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-4 col-xl">
        <div class="card-custom p-3 d-flex align-items-center flex-row gap-3">
            <div class="rounded-3 bg-danger bg-opacity-10 text-danger p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                <i class="fa-solid fa-circle-xmark fs-3"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Tidak Layak</div>
                <h3 class="fw-bold mb-0 text-danger">{{ $totalDitolak }}</h3>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-4 col-xl">
        <div class="card-custom p-3 d-flex align-items-center flex-row gap-3">
            <div class="rounded-3 bg-secondary bg-opacity-10 text-secondary p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                <i class="fa-solid fa-hourglass-half fs-3"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Menunggu</div>
                <h3 class="fw-bold mb-0 text-secondary">{{ $totalMenunggu }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-chart-line text-primary me-2"></i> Grafik Trend Pengajuan Bulanan</h6>
                <span class="badge bg-light text-secondary border">6 Bulan Terakhir</span>
            </div>
            <div style="height: 260px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-chart-pie text-cyan me-2"></i> Persentase Decision KPR</h6>
            </div>
            <div style="height: 200px;" class="d-flex justify-content-center align-items-center position-relative">
                <canvas id="decisionChart"></canvas>
            </div>
            <div class="d-flex justify-content-center gap-4 mt-3 pt-3 border-top small">
                <div><i class="fa-solid fa-circle text-success me-1"></i> Diterima ({{ $totalDiterima }})</div>
                <div><i class="fa-solid fa-circle text-danger me-1"></i> Ditolak ({{ $totalDitolak }})</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions & Recent Submissions -->
<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card-custom p-4 h-100">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-bolt text-warning me-2"></i> Quick Actions Admin</h6>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.applicants.create') }}" class="btn btn-primary rounded-3 text-start p-3 shadow-sm">
                    <i class="fa-solid fa-user-plus me-2"></i> <strong>Tambah Nasabah Baru</strong>
                    <div class="small opacity-75">Input data diri & nilai pengajuan KPR</div>
                </a>
                <a href="{{ route('admin.smart.engine') }}" class="btn btn-dark rounded-3 text-start p-3 shadow-sm">
                    <i class="fa-solid fa-microchip me-2 text-cyan"></i> <strong>Jalankan Mesin SMART</strong>
                    <div class="small opacity-75">Proses perhitungan otomatis & ranking</div>
                </a>
                <a href="{{ route('admin.criteria.index') }}" class="btn btn-light border rounded-3 text-start p-3">
                    <i class="fa-solid fa-sliders me-2 text-primary"></i> <strong>Kelola Kriteria & Bobot</strong>
                    <div class="small text-muted">Ubah bobot awal kriteria SMART</div>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Pengajuan KPR Terbaru</h6>
                <a href="{{ route('admin.applicants.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>No. Registrasi</th>
                            <th>Nama Nasabah</th>
                            <th>Nilai Pinjaman</th>
                            <th>Skor SMART</th>
                            <th>Keputusan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSubmissions as $sub)
                            <tr>
                                <td><span class="fw-bold text-primary">{{ $sub->no_pengajuan }}</span></td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $sub->user->name }}</div>
                                    <small class="text-muted">{{ $sub->user->profile->pekerjaan ?? '-' }}</small>
                                </td>
                                <td>Rp {{ number_format($sub->nilai_pinjaman, 0, ',', '.') }}</td>
                                <td>
                                    @if($sub->c1_verified_at && $sub->final_smart_score !== null)
                                        <span class="fw-bold fs-6">{{ number_format($sub->final_smart_score, 2) }}</span>
                                    @elseif(!$sub->c1_verified_at)
                                        <span class="badge bg-warning text-dark">Menunggu SLIK</span>
                                    @else
                                        <span class="badge bg-light text-muted border">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($sub->c1_verified_at && $sub->status_keputusan === 'LAYAK')
                                        <span class="badge-success-custom"><i class="fa-solid fa-circle-check me-1"></i> DITERIMA</span>
                                    @elseif($sub->c1_verified_at && $sub->status_keputusan === 'TIDAK LAYAK')
                                        <span class="badge-danger-custom"><i class="fa-solid fa-circle-xmark me-1"></i> TIDAK DITERIMA</span>
                                    @else
                                        <span class="badge-warning-custom"><i class="fa-solid fa-clock me-1"></i> Menunggu</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.applicants.show', $sub->id) }}" class="btn btn-sm btn-light border rounded-circle"><i class="fa-solid fa-eye text-secondary"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada pengajuan KPR.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const monthlyData = @json($monthlySubmissions);
    const labels = monthlyData.map(item => item.month);
    const dataCounts = monthlyData.map(item => item.total);

    const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctxMonthly, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Pengajuan',
                data: dataCounts,
                borderColor: '#2563EB',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    const ctxDecision = document.getElementById('decisionChart').getContext('2d');
    new Chart(ctxDecision, {
        type: 'doughnut',
        data: {
            labels: ['Diterima', 'Ditolak'],
            datasets: [{
                data: [{{ $totalDiterima }}, {{ $totalDitolak }}],
                backgroundColor: ['#10B981', '#EF4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { display: false } }
        }
    });
</script>
@endsection
