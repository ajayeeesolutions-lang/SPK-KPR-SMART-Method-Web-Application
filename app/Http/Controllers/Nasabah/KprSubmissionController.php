<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Http\Requests\KprSubmissionRequest;
use App\Models\KprSubmission;
use App\Services\UploadService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KprSubmissionController extends Controller
{
    public function create()
    {
        $user = auth()->user();
        if (!$user->profile) {
            return redirect()->route('nasabah.profile.edit')->with('warning', 'Harap melengkapi profil biodata Anda terlebih dahulu sebelum mengajukan KPR.');
        }

        $profile = $user->profile;
        return view('nasabah.submission.create', compact('user', 'profile'));
    }

    public function store(KprSubmissionRequest $request, UploadService $uploadService)
    {
        $user = auth()->user();

        $noPengajuan = 'NAS-' . date('Y') . '-' . Str::padLeft(KprSubmission::count() + 1, 3, '0');

        $submission = KprSubmission::create([
            'no_pengajuan'          => $noPengajuan,
            'user_id'               => $user->id,
            'harga_rumah'           => $request->harga_rumah,
            'uang_muka_dp'          => $request->uang_muka_dp,
            'nilai_pinjaman'        => $request->nilai_pinjaman,
            'tenor_tahun'           => $request->tenor_tahun,
            'status_pengajuan'      => 'pending',
            // C1 kredibilitas/SLIK diisi admin setelah pemeriksaan.
            'c2_penghasilan_bersih' => $request->c2_penghasilan_bersih,
            'c3_status_pekerjaan'   => $request->c3_status_pekerjaan,
            'c3_lama_bekerja_bulan' => $request->c3_lama_bekerja_bulan,
            'c4_usia'               => $request->c4_usia,
            'c5_jumlah_tanggungan'  => $request->c5_jumlah_tanggungan,
        ]);

        // Upload documents if present
        $docTypes = ['ktp', 'kk', 'slip_gaji', 'npwp', 'rekening_koran', 'sk_kerja', 'pendukung'];
        foreach ($docTypes as $doc) {
            if ($request->hasFile($doc)) {
                $uploadService->uploadDocument($submission, $doc, $request->file($doc));
            }
        }

        return redirect()->route('nasabah.dashboard')->with('success', "Pengajuan KPR No. {$noPengajuan} berhasil dikirim dan menunggu pemeriksaan admin.");
    }

    public function show(KprSubmission $submission)
    {
        if ($submission->user_id !== auth()->id() && !in_array(auth()->user()->role, ['admin', 'marketing', 'pimpinan'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        $submission->load(['user.profile', 'documents', 'smartResult']);
        return view('nasabah.submission.show', compact('submission'));
    }

    public function downloadPdf(KprSubmission $submission, ReportService $reportService)
    {
        if ($submission->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        $pdf = $reportService->generateSubmissionPdf($submission);
        return $pdf->download("Laporan_Analisis_KPR_{$submission->no_pengajuan}.pdf");
    }

    public function streamPdf(KprSubmission $submission, ReportService $reportService)
    {
        if ($submission->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        $pdf = $reportService->generateSubmissionPdf($submission);
        return $pdf->stream("Laporan_Analisis_KPR_{$submission->no_pengajuan}.pdf");
    }
}
