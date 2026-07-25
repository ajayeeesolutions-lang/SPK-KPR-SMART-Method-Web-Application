<?php

use App\Http\Controllers\Admin\AnalysisHistoryController;
use App\Http\Controllers\Admin\ApplicantController;
use App\Http\Controllers\Admin\CriterionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SmartEngineController;
use App\Http\Controllers\Admin\SubCriterionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Manager\ManagerApprovalController;
use App\Http\Controllers\Manager\ManagerDashboardController;
use App\Http\Controllers\Nasabah\KprSubmissionController;
use App\Http\Controllers\Nasabah\NasabahDashboardController;
use App\Http\Controllers\Nasabah\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'manager' => redirect()->route('manager.dashboard'),
            default => redirect()->route('nasabah.dashboard'),
        };
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('applicants', ApplicantController::class);
    Route::resource('criteria', CriterionController::class)->except(['create', 'edit', 'show']);
    Route::resource('sub-criteria', SubCriterionController::class)->except(['create', 'edit', 'show']);

    Route::get('/smart/engine', [SmartEngineController::class, 'index'])->name('smart.engine');
    Route::post('/smart/engine/{submission}', [SmartEngineController::class, 'runAnalysis'])->name('smart.analyze');

    Route::get('/history', [AnalysisHistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{submission}/pdf', [AnalysisHistoryController::class, 'downloadPdf'])->name('history.pdf');
    Route::get('/history/{submission}/stream', [AnalysisHistoryController::class, 'streamPdf'])->name('history.stream');

    Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});

// Manager Routes
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/submissions', [ManagerApprovalController::class, 'index'])->name('submissions.index');
    Route::get('/submissions/{submission}', [ManagerApprovalController::class, 'show'])->name('submissions.show');
    Route::post('/submissions/{submission}/approve', [ManagerApprovalController::class, 'approve'])->name('submissions.approve');
    Route::post('/submissions/{submission}/reject', [ManagerApprovalController::class, 'reject'])->name('submissions.reject');
});

// Nasabah Routes
Route::middleware(['auth', 'role:nasabah'])->prefix('nasabah')->name('nasabah.')->group(function () {
    Route::get('/dashboard', [NasabahDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/submission/create', [KprSubmissionController::class, 'create'])->name('submission.create');
    Route::post('/submission', [KprSubmissionController::class, 'store'])->name('submission.store');
    Route::get('/submission/{submission}', [KprSubmissionController::class, 'show'])->name('submission.show');
});
