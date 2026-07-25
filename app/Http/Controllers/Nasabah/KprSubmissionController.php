<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Http\Requests\KprSubmissionRequest;
use App\Models\KprSubmission;
use App\Services\SmartService;
use App\Services\UploadService;
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

        return view('nasabah.submission.create', compact('user'));
    }

    public function store(KprSubmissionRequest $request, UploadService $uploadService, SmartService $smartService)
    {
        $user = auth()->user();

        $noPengajuan = 'NAS-' . date('Y') . '-' . Str::padLeft(KprSubmission::count() + 1, 3, '0');

        $submission = KprSubmission::create([
            'no_pengajuan' => $noPengajuan,
            'user_id' => $user->id,
            'harga_rumah' => $request->harga_rumah,
            'uang_muka_dp' => $request->uang_muka_dp,
            'nilai_pinjaman' => $request->nilai_pinjaman,
            'tenor_tahun' => $request->tenor_tahun,
            'status_pengajuan' => 'pending',
        ]);

        // Upload documents if present
        $docTypes = ['ktp', 'kk', 'slip_gaji', 'npwp', 'rekening_koran', 'sk_kerja', 'pendukung'];
        foreach ($docTypes as $doc) {
            if ($request->hasFile($doc)) {
                $uploadService->uploadDocument($submission, $doc, $request->file($doc));
            }
        }

        // Run SMART Engine automatically
        try {
            $smartService->analyzeSubmission($submission);
        } catch (\Exception $e) {}

        return redirect()->route('nasabah.dashboard')->with('success', "Pengajuan KPR No. {$noPengajuan} berhasil dikirim dan dianalisis!");
    }

    public function show(KprSubmission $submission)
    {
        if ($submission->user_id !== auth()->id() && !in_array(auth()->user()->role, ['admin', 'manager'])) {
            abort(403);
        }

        $submission->load(['user.profile', 'documents', 'smartResult']);
        return view('nasabah.submission.show', compact('submission'));
    }
}
