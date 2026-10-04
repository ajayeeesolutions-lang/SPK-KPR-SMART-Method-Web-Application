<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\KprSubmission;
use Illuminate\Http\Request;

class ManagerApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = KprSubmission::with(['user.profile', 'smartResult']);

        if ($request->filled('status')) {
            $query->where('status_pengajuan', $request->status);
        }

        $submissions = $query->latest()->paginate(10);
        return view('manager.submissions.index', compact('submissions'));
    }

    public function show(KprSubmission $submission)
    {
        $submission->load(['user.profile', 'documents', 'smartResult', 'approver']);
        return view('manager.submissions.show', compact('submission'));
    }

    public function approve(Request $request, KprSubmission $submission)
    {
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
}
