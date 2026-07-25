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
        $totalDiterima = KprSubmission::where('status_keputusan', 'DITERIMA')->count();
        $totalDitolak = KprSubmission::where('status_keputusan', 'TIDAK DITERIMA')->count();
        $totalPendingApproval = KprSubmission::where('status_pengajuan', 'analyzed')->count();

        $recentSubmissions = KprSubmission::with(['user.profile', 'smartResult'])
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
            'totalDitolak',
            'totalPendingApproval',
            'recentSubmissions',
            'monthlyStats'
        ));
    }
}
