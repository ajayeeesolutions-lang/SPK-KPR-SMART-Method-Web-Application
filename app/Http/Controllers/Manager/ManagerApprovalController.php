<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\KprSubmission;
use Illuminate\Http\Request;

class ManagerApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = KprSubmission::with(['user.profile', 'smartResult'])
            ->whereNotNull('c1_verified_at')
            ->whereNotNull('final_smart_score');

        if ($request->filled('status')) {
            $query->where('status_pengajuan', $request->status);
        }

        $submissions = $query->latest()->get();
        return view('manager.submissions.index', compact('submissions'));
    }

    public function show(KprSubmission $submission)
    {
        $submission->load(['user.profile', 'documents', 'smartResult', 'approver']);
        return view('manager.submissions.show', compact('submission'));
    }

    public function approve(Request $request, KprSubmission $submission)
    {
        abort_unless($submission->c1_verified_at && $submission->smartResult, 403, 'Admin harus mengonfirmasi kredibilitas SLIK sebelum pengajuan dapat diputuskan.');

        $request->validate([
            'manager_notes' => 'nullable|string|max:1000',
        ]);

        $submission->update([
            'status_pengajuan' => 'approved',
            'manager_notes' => $request->manager_notes,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('manager.submissions.show', $submission->id)
            ->with('success', "Pengajuan {$submission->no_pengajuan} berhasil disetujui (APPROVED).");
    }

    public function reject(Request $request, KprSubmission $submission)
    {
        abort_unless($submission->c1_verified_at && $submission->smartResult, 403, 'Admin harus mengonfirmasi kredibilitas SLIK sebelum pengajuan dapat diputuskan.');

        $request->validate([
            'manager_notes' => 'required|string|max:1000',
        ]);

        $submission->update([
            'status_pengajuan' => 'rejected',
            'manager_notes' => $request->manager_notes,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('manager.submissions.show', $submission->id)
            ->with('success', "Pengajuan {$submission->no_pengajuan} ditolak (REJECTED).");
    }

    public function exportExcel()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Laporan_Pengajuan_KPR.csv"',
        ];

        $submissions = KprSubmission::with(['user.profile', 'smartResult'])->get();

        $callback = function() use ($submissions) {
            $file = fopen('php://output', 'w');
            $columns = [
                'No. Pengajuan', 'Nama Nasabah', 'NIK',
                'Plafon KPR (Rp)', 'Skor SMART', 'Rekomendasi', 'Status Pengajuan'
            ];
            fputcsv($file, $columns);

            foreach ($submissions as $sub) {
                fputcsv($file, [
                    $sub->no_pengajuan,
                    $sub->user->name,
                    $sub->user->profile?->nik ?? '-',
                    $sub->nilai_pinjaman,
                    $sub->smartResult?->total_score ?? '-',
                    $sub->smartResult?->decision ?? '-',
                    $sub->status_pengajuan
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $submissions = KprSubmission::with(['user.profile', 'smartResult'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('manager.submissions.pdf', compact('submissions'))
            ->setPaper('a4', 'landscape');
            
        return $pdf->download('Laporan_Pengajuan_KPR_Manager.pdf');
    }
}

