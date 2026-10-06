@extends('layouts.app')

@section('title', 'Detail Pengajuan - ' . $submission->no_pengajuan)
@section('page-title', 'Detail Pengajuan KPR Nasabah')

@section('content')

{{-- Header Status --}}
<div class="card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <span class="badge bg-primary text-uppercase fs-6 mb-2">{{ $submission->no_pengajuan }}</span>
            <h4 class="fw-bold text-dark mb-1">Status Pengajuan KPR Anda</h4>
            <div class="text-muted small">Diajukan pada: {{ $submission->created_at->translatedFormat('d F Y, H:i') }}</div>
        </div>
        <div class="text-end">
            @php
                $sp = $submission->status_pengajuan;
            @endphp
            @if($submission->c1_verified_at && $sp === 'approved')
                <span class="badge bg-success fs-6 px-3 py-2"><i class="fa-solid fa-circle-check me-1"></i> DISETUJUI</span>
            @elseif($submission->c1_verified_at && $sp === 'rejected')
                <span class="badge bg-danger fs-6 px-3 py-2"><i class="fa-solid fa-circle-xmark me-1"></i> DITOLAK</span>
            @elseif($sp === 'analyzed' && $submission->c1_verified_at)
                <span class="badge bg-info fs-6 px-3 py-2"><i class="fa-solid fa-magnifying-glass me-1"></i> SUDAH DIANALISIS</span>
            @else
                <span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="fa-solid fa-clock me-1"></i> MENUNGGU ANALISIS</span>
            @endif
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Kiri: Kriteria & Hasil SMART --}}
    <div class="col-12 col-lg-8">

        {{-- Tabel Data Input Kriteria SMART --}}
        <div class="card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-table-list text-primary me-2"></i> Data Input Kriteria SMART (Matriks Alternatif)</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px;">Kode</th>
                            <th>Kriteria</th>
                            <th>Nilai Input</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge bg-success">C1</span></td>
                            <td>Riwayat SLIK OJK</td>
                            <td class="fw-semibold">
                                @if($submission->c1_verified_at)
                                    {{ $submission->c1_riwayat_kredit }}
                                @else
                                    <span class="text-muted">Menunggu konfirmasi admin</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-primary">C2</span></td>
                            <td>Penghasilan Bersih/Bulan</td>
                            <td class="fw-semibold">Rp {{ number_format($submission->c2_penghasilan_bersih, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-warning text-dark">C3</span></td>
                            <td>Status Pekerjaan</td>
                            <td class="fw-semibold">{{ $submission->c3_status_pekerjaan }} <span class="text-muted">({{ $submission->c3_lama_bekerja_bulan }} bulan)</span></td>
                        </tr>
                        <tr>
                            <td><span class="badge" style="background:#9333EA;">C4</span></td>
                            <td>Usia</td>
                            <td class="fw-semibold">{{ $submission->c4_usia }} Tahun</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-danger">C5</span></td>
                            <td>Jumlah Tanggungan</td>
                            <td class="fw-semibold">{{ $submission->c5_jumlah_tanggungan }} Orang</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Data Properti & Pinjaman --}}
        <div class="card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-house text-primary me-2"></i> Data Properti & Pinjaman</h6>
            <div class="row text-center g-3">
                <div class="col-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small">Harga Rumah</div>
                        <div class="fw-bold text-dark small">Rp {{ number_format($submission->harga_rumah, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small">Uang Muka (DP)</div>
                        <div class="fw-bold text-dark small">Rp {{ number_format($submission->uang_muka_dp, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small">Nilai Pinjaman</div>
                        <div class="fw-bold text-primary small">Rp {{ number_format($submission->nilai_pinjaman, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small">Tenor</div>
                        <div class="fw-bold text-dark small">{{ $submission->tenor_tahun }} Tahun</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hasil SMART Engine --}}
        @if($submission->c1_verified_at && $submission->final_smart_score !== null)
        <div class="card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-robot text-primary me-2"></i> Hasil Analisis Mesin SMART</h6>
            <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded-3">
                <div class="text-center">
                    <div class="text-muted small">Skor Akhir SMART</div>
                    <div class="fw-bold text-primary" style="font-size: 2rem;">{{ number_format($submission->final_smart_score, 4) }}</div>
                    <div class="text-muted small">dari 1.0000</div>
                </div>
                <div class="border-start ps-3">
                    @if($submission->status_keputusan === 'LAYAK')
                        <div class="fw-bold text-success fs-5"><i class="fa-solid fa-circle-check me-1"></i> LAYAK</div>
                        <div class="text-muted small">Memenuhi threshold kelayakan bank.</div>
                    @elseif($submission->status_keputusan === 'DIPERTIMBANGKAN')
                        <div class="fw-bold text-warning fs-5"><i class="fa-solid fa-circle-exclamation me-1"></i> DIPERTIMBANGKAN</div>
                        <div class="text-muted small">Skor cukup baik, butuh evaluasi lebih lanjut.</div>
                    @elseif($submission->status_keputusan === 'TIDAK LAYAK')
                        <div class="fw-bold text-danger fs-5"><i class="fa-solid fa-circle-xmark me-1"></i> TIDAK LAYAK</div>
                        <div class="text-muted small">Belum memenuhi standar kelayakan bank.</div>
                    @endif
                </div>
            </div>
            @if(isset($submission->smartResult->explanations['summary']))
                <div class="small text-muted">
                    <ul class="ps-3 mb-0">
                        @foreach($submission->smartResult->explanations['summary'] as $reason)
                            <li>{{ $reason }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        @else
        <div class="alert alert-warning border-0 rounded-3 small">
            <i class="fa-solid fa-hourglass-half me-2"></i>
            @if(!$submission->c1_verified_at)
                Pengajuan masih menunggu konfirmasi kredibilitas SLIK dari admin sebelum analisis SMART dapat ditampilkan.
            @else
                Kredibilitas SLIK telah dikonfirmasi. Analisis SMART akan tersedia setelah diproses admin.
            @endif
        </div>
        @endif

        {{-- Catatan Manager --}}
        @if($submission->c1_verified_at && $submission->manager_notes)
        <div class="alert alert-info border-0 rounded-3 small">
            <i class="fa-solid fa-comment-dots me-2"></i>
            <strong>Catatan Keputusan Pimpinan:</strong> {{ $submission->manager_notes }}
        </div>
        @endif
    </div>

    {{-- Kanan: Aksi & Notifikasi PDF --}}
    <div class="col-12 col-lg-4">
        <div class="card-custom p-4 text-center">
            <i class="fa-solid fa-file-pdf text-danger mb-3" style="font-size: 3rem;"></i>
            <h6 class="fw-bold text-dark mb-2">Surat Keputusan KPR</h6>
            <p class="text-muted small mb-4">Surat keputusan resmi hasil analisis kelayakan KPR Anda hanya dapat dicetak oleh tim Marketing atau Admin. Silakan hubungi kami untuk mendapatkan salinan.</p>

            <button type="button" onclick="showPdfNotice()" class="btn btn-danger w-100 rounded-3 shadow mb-2">
                <i class="fa-solid fa-download me-2"></i> Unduh Hasil PDF
            </button>
            <button type="button" onclick="showPdfNotice()" class="btn btn-outline-secondary w-100 rounded-3 mb-4">
                <i class="fa-solid fa-eye me-2"></i> Preview Surat
            </button>

            <a href="{{ route('nasabah.dashboard') }}" class="btn btn-light border w-100 rounded-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function showPdfNotice() {
        Swal.fire({
            icon: 'info',
            title: 'Akses Dibatasi',
            html: 'Berdasarkan prosedur, dokumen PDF resmi hanya dapat dicetak oleh <strong>Admin</strong> atau <strong>Marketing</strong>.<br><br>Silakan hubungi tim kami untuk mendapatkan salinan surat keputusan KPR Anda.',
            confirmButtonText: 'Baik, Mengerti',
            confirmButtonColor: '#2563EB',
        });
    }
</script>
@endsection
