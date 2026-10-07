<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KprSubmission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPengajuan = KprSubmission::count();
        $totalDiterima = KprSubmission::whereNotNull('c1_verified_at')->where('status_keputusan', 'LAYAK')->count();
        $totalDitolak = KprSubmission::whereNotNull('c1_verified_at')->where('status_keputusan', 'TIDAK LAYAK')->count();
        $totalDipertimbangkan = KprSubmission::whereNotNull('c1_verified_at')->where('status_keputusan', 'DIPERTIMBANGKAN')->count();
        $totalMenunggu = KprSubmission::whereIn('status_pengajuan', ['pending', 'analyzed'])->count();
        $totalNasabah = User::where('role', 'debitur')->count();

        // Monthly trends data for Chart.js (Database-Agnostic for SQLite & MySQL)
        // Get last 6 months
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

        $monthlySubmissions = [];
        foreach ($months as $month => $total) {
            $monthlySubmissions[] = (object) [
                'month' => $month,
                'total' => $total
            ];
        }

        $recentSubmissions = KprSubmission::with(['user.profile'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPengajuan',
            'totalDiterima',
            'totalDipertimbangkan',
            'totalDitolak',
            'totalMenunggu',
            'totalNasabah',
            'monthlySubmissions',
            'recentSubmissions'
        ));
    }
}
