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
        $monthlySubmissions = KprSubmission::oldest()
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('M Y');
            })
            ->map(function ($items, $month) {
                return (object) [
                    'month' => $month,
                    'total' => $items->count(),
                ];
            })
            ->take(6)
            ->values();

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
