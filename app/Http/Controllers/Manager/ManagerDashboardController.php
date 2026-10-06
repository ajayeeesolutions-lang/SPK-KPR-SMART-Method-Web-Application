<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\KprSubmission;
use Illuminate\Support\Facades\DB;

class ManagerDashboardController extends Controller
{
    public function index()
    {
        $totalPengajuan = KprSubmission::count();
        $verifiedSubmissions = KprSubmission::whereNotNull('c1_verified_at')->whereNotNull('final_smart_score');
        $totalDiterima = (clone $verifiedSubmissions)->where('status_keputusan', 'LAYAK')->count();
        $totalDitolak = (clone $verifiedSubmissions)->where('status_keputusan', 'TIDAK LAYAK')->count();
        $totalDipertimbangkan = (clone $verifiedSubmissions)->where('status_keputusan', 'DIPERTIMBANGKAN')->count();
        $totalPendingApproval = (clone $verifiedSubmissions)->where('status_pengajuan', 'analyzed')->count();

        $recentSubmissions = KprSubmission::with(['user.profile', 'smartResult'])
            ->whereNotNull('c1_verified_at')
            ->whereNotNull('final_smart_score')
            ->latest()
            ->take(5)
            ->get();

        $monthlyStats = KprSubmission::oldest()
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('b Y');
            })
            ->map(function ($items, $month) {
                return (object) [
                    'month' => $month,
                    'total' => $items->count(),
                ];
            })
            ->take(6)
            ->values();

        return view('manager.dashboard', compact(
            'totalPengajuan',
            'totalDiterima',
            'totalDipertimbangkan',
            'totalDitolak',
            'totalPendingApproval',
            'recentSubmissions',
            'monthlyStats'
        ));
    }
}
