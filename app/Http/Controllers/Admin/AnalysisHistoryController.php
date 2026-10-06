<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KprSubmission;
use App\Services\ReportService;
use Illuminate\Http\Request;

class AnalysisHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = KprSubmission::with(['user.profile', 'smartResult', 'approver'])
            ->whereNotNull('final_smart_score')
            ->whereNotNull('c1_verified_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status_keputusan', $request->status);
        }

        $history = $query->latest('updated_at')->paginate(10);

        return view('admin.history.index', compact('history'));
    }

    public function downloadPdf(KprSubmission $submission, ReportService $reportService)
    {
        abort_unless($submission->c1_verified_at && $submission->smartResult, 404);

        $pdf = $reportService->generateSubmissionPdf($submission);
        return $pdf->download("Laporan_Analisis_KPR_{$submission->no_pengajuan}.pdf");
    }

    public function streamPdf(KprSubmission $submission, ReportService $reportService)
    {
        abort_unless($submission->c1_verified_at && $submission->smartResult, 404);

        $pdf = $reportService->generateSubmissionPdf($submission);
        return $pdf->stream("Laporan_Analisis_KPR_{$submission->no_pengajuan}.pdf");
    }
}
