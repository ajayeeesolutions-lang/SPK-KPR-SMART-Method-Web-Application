<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\KprSubmission;

class NasabahDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $profile = $user->profile;

        $submissions = KprSubmission::with(['smartResult', 'documents'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $activeSubmission = $submissions->first();

        return view('nasabah.dashboard', compact('user', 'profile', 'submissions', 'activeSubmission'));
    }
}
