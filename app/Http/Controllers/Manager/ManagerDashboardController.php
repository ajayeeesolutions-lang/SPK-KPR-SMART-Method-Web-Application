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

        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[now()->subMonths($i)->format('M Y')] = 0;
        }

        $submissions = KprSubmission::where('created_at', '>=', now()->subMonths(5)->startOfMonth())->get();

        foreach ($submissions as $sub) {
            $month = $sub->created_at->format('M Y');
            if (isset($months[$month])) {
                $months[$month]++;
            }
        }

        $monthlyStats = [];
        foreach ($months as $month => $total) {
            $monthlyStats[] = (object) [
                'month' => $month,
                'total' => $total
            ];
        }

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
