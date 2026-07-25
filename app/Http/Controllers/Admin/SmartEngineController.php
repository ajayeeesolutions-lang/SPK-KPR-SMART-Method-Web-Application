<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Criterion;
use App\Models\KprSubmission;
use App\Models\Setting;
use App\Services\SmartService;
use Illuminate\Http\Request;

class SmartEngineController extends Controller
{
    public function index(Request $request, SmartService $smartService)
    {
        $criteria = Criterion::with('subCriteria')->where('is_active', true)->orderBy('code')->get();
        $threshold = (float) Setting::getByKey('smart_threshold', 80.00);

        // Fetch submissions that need or have analysis
        $submissions = KprSubmission::with(['user.profile', 'smartResult'])
            ->latest()
            ->get();

        $selectedSubmissionId = $request->query('submission_id');

        $activeSubmission = null;
        $smartResult = null;

        if ($selectedSubmissionId) {
            $activeSubmission = KprSubmission::with(['user.profile', 'documents', 'smartResult'])->find($selectedSubmissionId);
        } elseif ($submissions->count() > 0) {
            $activeSubmission = $submissions->first();
        }

        if ($activeSubmission) {
            try {
                $smartResult = $smartService->analyzeSubmission($activeSubmission);
            } catch (\Exception $e) {
                // handle case where profile is not complete
            }
        }

        // System Rankings of all analyzed submissions
        $rankings = KprSubmission::with(['user.profile', 'smartResult'])
            ->whereNotNull('final_smart_score')
            ->orderBy('final_smart_score', 'desc')
            ->get();

        return view('admin.smart.engine', compact(
            'criteria',
            'threshold',
            'submissions',
            'activeSubmission',
            'smartResult',
            'rankings'
        ));
    }

    public function runAnalysis(Request $request, KprSubmission $submission, SmartService $smartService)
    {
        try {
            $smartService->analyzeSubmission($submission);
            return redirect()->route('admin.smart.engine', ['submission_id' => $submission->id])
                ->with('success', "Analisis SMART untuk pengajuan {$submission->no_pengajuan} berhasil dijalankan!");
        } catch (\Exception $e) {
            return back()->with('error', "Gagal menjalankan analisis SMART: " . $e->getMessage());
        }
    }
}
