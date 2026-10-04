<?php

namespace App\Http\Controllers;

use App\Models\KprSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RealtimeController extends Controller
{
    /**
     * Endpoint ringan untuk polling realtime.
     * Dipanggil setiap N detik dari frontend untuk cek apakah ada data baru.
     * Return: timestamp terbaru yang relevan per role.
     */
    public function checkUpdates(): JsonResponse
    {
        $user = Auth::user();
        $role = $user->role;

        $data = [
            'timestamp' => now()->toISOString(),
            'has_update' => false,
            'message'    => null,
            'type'       => null,
        ];

        if ($role === 'debitur') {
            // Debitur: cek apakah ada perubahan status pengajuan mereka
            $latest = KprSubmission::where('user_id', $user->id)
                ->orderByDesc('updated_at')
                ->first(['no_pengajuan', 'status_pengajuan', 'status_keputusan', 'updated_at']);

            if ($latest) {
                $data['last_updated']  = $latest->updated_at->toISOString();
                $data['status']        = $latest->status_pengajuan;
                $data['keputusan']     = $latest->status_keputusan;
                $data['no_pengajuan']  = $latest->no_pengajuan;
            }

        } elseif (in_array($role, ['admin', 'marketing'])) {
            // Admin/Marketing: cek submission baru yang masuk (pending)
            $latestSubmission = KprSubmission::orderByDesc('updated_at')->first(['updated_at']);
            $pendingCount     = KprSubmission::where('status_pengajuan', 'pending')->count();
            $analyzedCount    = KprSubmission::where('status_pengajuan', 'analyzed')->count();

            $data['last_updated']   = $latestSubmission?->updated_at->toISOString();
            $data['pending_count']  = $pendingCount;
            $data['analyzed_count'] = $analyzedCount;
            $data['total']          = KprSubmission::count();

        } elseif ($role === 'pimpinan') {
            // Pimpinan: cek submission yang sudah dianalisis dan butuh persetujuan
            $latestSubmission = KprSubmission::orderByDesc('updated_at')->first(['updated_at']);
            $needApproval     = KprSubmission::where('status_pengajuan', 'analyzed')->count();
            $approved         = KprSubmission::where('status_pengajuan', 'approved')->count();
            $rejected         = KprSubmission::where('status_pengajuan', 'rejected')->count();

            $data['last_updated']    = $latestSubmission?->updated_at->toISOString();
            $data['need_approval']   = $needApproval;
            $data['approved_count']  = $approved;
            $data['rejected_count']  = $rejected;
            $data['total']           = KprSubmission::count();
        }

        return response()->json($data);
    }
}
