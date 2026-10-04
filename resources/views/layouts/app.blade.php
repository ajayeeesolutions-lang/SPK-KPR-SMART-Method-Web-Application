<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SPK Kelayakan KPR - SMART Method') - Bank KPR Sejahtera</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <!-- PWA Settings -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0F172A">
    <link rel="apple-touch-icon" href="{{ asset('pwa-icon-192.png') }}">


    <style>
        :root {
            --bank-primary: #0F172A;
            --bank-accent: #2563EB;
            --bank-accent-hover: #1D4ED8;
            --bank-cyan: #06B6D4;
            --bank-emerald: #10B981;
            --bank-rose: #EF4444;
            --bank-bg: #F1F5F9;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background-color: var(--bank-bg);
            color: #334155;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar-wrapper {
            min-height: 100vh;
            width: 270px;
            background: linear-gradient(180deg, #0F172A 0%, #1E293B 100%);
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 1.75rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-brand-title {
            color: #FFFFFF;
            font-weight: 800;
            font-size: 1.2rem;
            letter-spacing: -0.5px;
        }

        .sidebar-nav {
            padding: 1.25rem 0.85rem;
        }

        .nav-category {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748B;
            padding: 1rem 0.85rem 0.4rem 0.85rem;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.1rem;
            color: #94A3B8;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 0.75rem;
            text-decoration: none;
            margin-bottom: 0.35rem;
            transition: all 0.2s ease;
        }

        .nav-link-custom i {
            font-size: 1.1rem;
            width: 1.8rem;
            text-align: center;
            margin-right: 0.5rem;
        }

        .nav-link-custom:hover {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.08);
        }

        .nav-link-custom.active {
            color: #FFFFFF;
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            box-shadow: 0 8px 16px -4px rgba(37, 99, 235, 0.4);
        }

        /* Top Navbar */
        #navbar-wrapper {
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            padding: 1rem 2rem;
        }

        .card-custom {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-custom:hover {
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.06), 0 6px 10px -2px rgba(0, 0, 0, 0.03);
        }

        .badge-success-custom {
            background-color: #DCFCE7;
            color: #15803D;
            font-weight: 700;
            padding: 0.4em 0.85em;
            border-radius: 0.6rem;
            border: 1px solid #BBF7D0;
            white-space: nowrap !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .badge-danger-custom {
            background-color: #FEE2E2;
            color: #B91C1C;
            font-weight: 700;
            padding: 0.4em 0.85em;
            border-radius: 0.6rem;
            border: 1px solid #FECACA;
            white-space: nowrap !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .badge-warning-custom {
            background-color: #FEF3C7;
            color: #B45309;
            font-weight: 700;
            padding: 0.4em 0.85em;
            border-radius: 0.6rem;
            border: 1px solid #FDE68A;
            white-space: nowrap !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        /* SMART Wizard Step Progress */
        .smart-wizard-nav {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 2rem;
        }

        .smart-wizard-nav::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 3px;
            background: #E2E8F0;
            z-index: 1;
            transform: translateY(-50%);
        }

        .wizard-step-item {
            position: relative;
            z-index: 2;
            background: #FFFFFF;
            padding: 0 0.5rem;
            text-align: center;
        }

        .wizard-step-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #E2E8F0;
            color: #64748B;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            margin: 0 auto 0.5rem auto;
            transition: all 0.3s ease;
        }

        .wizard-step-item.active .wizard-step-circle {
            background: var(--bank-accent);
            color: #FFFFFF;
            box-shadow: 0 0 0 5px rgba(37, 99, 235, 0.2);
        }

        .wizard-step-item.completed .wizard-step-circle {
            background: var(--bank-emerald);
            color: #FFFFFF;
        }

        .wizard-step-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748B;
        }
    </style>
    @yield('styles')
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <div class="sidebar-brand d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center shadow" style="width: 42px; height: 42px; overflow: hidden; background: white;">
                <img src="{{ asset('pwa-icon-192.png') }}" alt="Logo App" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div>
                <div class="sidebar-brand-title">SPK KPR SMART</div>
                <div class="text-secondary small fw-medium">PT Citra Pasada Properti</div>
            </div>
        </div>

        <div class="sidebar-nav">
            @if(auth()->user()->isAdmin())
                {{-- ====== MENU ADMIN (Akses Penuh) ====== --}}
                <div class="nav-category">Main Menu</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-link-custom {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>

                <div class="nav-category">Data Master</div>
                <a href="{{ route('admin.applicants.index') }}" class="nav-link-custom {{ request()->routeIs('admin.applicants.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i> Data Nasabah KPR
                </a>
                <a href="{{ route('admin.criteria.index') }}" class="nav-link-custom {{ request()->routeIs('admin.criteria.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-sliders"></i> Master Kriteria & Bobot
                </a>
                <a href="{{ route('admin.sub-criteria.index') }}" class="nav-link-custom {{ request()->routeIs('admin.sub-criteria.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group"></i> Sub Kriteria & Utility
                </a>

                <div class="nav-category">Mesin Decision</div>
                <a href="{{ route('admin.smart.engine') }}" class="nav-link-custom {{ request()->routeIs('admin.smart.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-microchip"></i> Proses Analisis SMART
                </a>
                <a href="{{ route('admin.history.index') }}" class="nav-link-custom {{ request()->routeIs('admin.history.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Analisis & Report
                </a>

                <div class="nav-category">Pengaturan</div>
                <a href="{{ route('admin.users.index') }}" class="nav-link-custom {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-gear"></i> Kelola User Role
                </a>
                <a href="{{ route('admin.settings.index') }}" class="nav-link-custom {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gears"></i> Threshold & System
                </a>

            @elseif(auth()->user()->isMarketing())
                {{-- ====== MENU MARKETING (Sesuai Naskah Tabel 3.1) ====== --}}
                {{-- Marketing: input/ubah data debitur, analisis SMART, lihat laporan --}}
                {{-- Marketing TIDAK bisa: kelola kriteria, sub kriteria, kelola user, settings --}}
                <div class="nav-category">Main Menu</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-link-custom {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>

                <div class="nav-category">Data Debitur</div>
                <a href="{{ route('admin.applicants.index') }}" class="nav-link-custom {{ request()->routeIs('admin.applicants.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i> Data Nasabah KPR
                </a>

                <div class="nav-category">Analisis SMART</div>
                <a href="{{ route('admin.smart.engine') }}" class="nav-link-custom {{ request()->routeIs('admin.smart.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-microchip"></i> Proses Analisis SMART
                </a>
                <a href="{{ route('admin.history.index') }}" class="nav-link-custom {{ request()->routeIs('admin.history.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i> Riwayat & Laporan
                </a>

            @elseif(auth()->user()->isManager())
                <div class="nav-category">Portal Pimpinan</div>
                <a href="{{ route('manager.dashboard') }}" class="nav-link-custom {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i> Executive Dashboard
                </a>
                <a href="{{ route('manager.submissions.index') }}" class="nav-link-custom {{ request()->routeIs('manager.submissions.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clipboard-check"></i> Laporan & Keputusan KPR
                </a>
            @else
                <div class="nav-category">Portal Debitur</div>
                <a href="{{ route('nasabah.dashboard') }}" class="nav-link-custom {{ request()->routeIs('nasabah.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-house-user"></i> Dashboard Pengajuan
                </a>
                <a href="{{ route('nasabah.profile.edit') }}" class="nav-link-custom {{ request()->routeIs('nasabah.profile.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-id-card"></i> Profil Biodata
                </a>
                <a href="{{ route('nasabah.submission.create') }}" class="nav-link-custom {{ request()->routeIs('nasabah.submission.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-paper-plane"></i> Ajukan KPR Baru
                </a>
            @endif
        </div>
    </div>

    <!-- Page Content -->
    <div class="w-100 d-flex flex-column min-vh-100">
        <!-- Navbar Header -->
        <div id="navbar-wrapper" class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">@yield('page-title', 'Dashboard System')</h5>
                <small class="text-muted">Sistem Pendukung Keputusan KPR Method SMART</small>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-md-block">
                    <div class="fw-bold small text-dark">{{ auth()->user()->name }}</div>
                    <div class="badge bg-primary text-capitalize" style="font-size: 0.65rem;">{{ auth()->user()->role }}</div>
                </div>
                
                <div class="dropdown">
                    <button class="btn btn-light rounded-circle shadow-sm border p-0 overflow-hidden" style="width: 42px; height: 42px;" type="button" data-bs-toggle="dropdown">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <i class="fa-solid fa-user-circle fs-3 text-secondary" style="line-height: 40px;"></i>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-2">
                        <li>
                            <div class="dropdown-header">
                                <strong>{{ auth()->user()->name }}</strong><br>
                                <small class="text-muted">{{ auth()->user()->email }}</small>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        
                        @if(auth()->user()->role !== 'debitur')
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#avatarModal">
                                <i class="fa-solid fa-camera fa-fw me-2 text-secondary"></i> Ganti Foto Profil
                            </a>
                        </li>
                        @endif

                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content Body -->
        <div class="p-4 p-md-5 flex-grow-1">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="fa-solid fa-circle-xmark me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 text-center text-muted small">
            &copy; {{ date('Y') }} <strong>Bank KPR Sejahtera Indonesia</strong> - Decision Support System Method SMART.
        </footer>
    </div>
</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('.datatable-custom').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            }
        });
    });

    // PWA Service Worker Registration
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js').then(function(registration) {
                console.log('ServiceWorker registration successful with scope: ', registration.scope);
            }, function(err) {
                console.log('ServiceWorker registration failed: ', err);
            });
        });
    }
</script>

<!-- Avatar Upload Modal (Untuk Admin/Manager/Marketing) -->
@if(auth()->user()->role !== 'debitur')
<div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="avatarModalLabel">Ganti Foto Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center overflow-hidden mb-3" style="width: 100px; height: 100px;">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <i class="fa-solid fa-user text-secondary" style="font-size: 3rem;"></i>
                    @endif
                </div>
                <div class="mb-3">
                    <input class="form-control" type="file" name="avatar" accept="image/*" required>
                    <div class="form-text small">Gunakan gambar persegi (maksimal 2MB).</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Simpan Foto</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Floating WhatsApp Button -->
@php
    $adminWaNumber = \App\Models\Setting::getByKey('admin_wa', '6281234567890');
    $waText = "Halo Admin KPR SMART, ";
    
    if (auth()->check()) {
        $waText .= "saya *" . auth()->user()->name . "* ";
        
        if (auth()->user()->role === 'debitur') {
            $latestSubmission = \App\Models\KprSubmission::where('user_id', auth()->id())->latest()->first();
            if ($latestSubmission) {
                $waText .= "(No Pengajuan: " . $latestSubmission->no_pengajuan . ", diinput pada *" . $latestSubmission->created_at->translatedFormat('d F Y') . "*), ";
            }
        }
        $waText .= "mohon bantuannya terkait aplikasi KPR saya.";
    } else {
        $waText .= "saya butuh informasi lebih lanjut terkait pengajuan KPR.";
    }
@endphp
<a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $adminWaNumber) }}?text={{ urlencode($waText) }}" 
   target="_blank" 
   class="btn btn-success rounded-circle shadow-lg d-flex align-items-center justify-content-center" 
   style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 9999; font-size: 30px;"
   title="Live Chat WhatsApp Admin">
    <i class="fa-brands fa-whatsapp text-white"></i>
</a>

{{-- Global SweetAlert untuk semua tombol hapus (btn-swal-del) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-swal-del').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const label = this.dataset.label || 'item ini';
            const form  = this.closest('form');
            Swal.fire({
                icon: 'warning',
                title: 'Hapus ' + label + '?',
                html: '<strong>' + label + '</strong> akan dihapus permanen dan tidak dapat dikembalikan.',
                showCancelButton: true,
                confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#94A3B8',
                reverseButtons: true,
            }).then(function (result) {
                if (result.isConfirmed) form.submit();
            });
        });
    });
});
</script>

@yield('scripts')

{{-- ============================================================
     REALTIME POLLING — Auto-refresh setiap 20 detik jika ada data baru
     Silent refresh tanpa pop-up, per role
     ============================================================ --}}
<script>
(function () {
    'use strict';

    const POLL_INTERVAL = 5000; // 5 detik
    const ROLE      = @json(auth()->user()->role ?? '');
    const CHECK_URL = '{{ route("realtime.check") }}';

    let lastKnownState = null;

    // Langsung reload halaman tanpa tanya
    function silentReload() {
        window.location.reload();
    }

    // Fungsi polling utama
    async function pollUpdates() {
        try {
            const response = await fetch(CHECK_URL, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) return;
            const data = await response.json();

            // Inisialisasi state awal — jangan reload saat pertama kali
            if (lastKnownState === null) {
                lastKnownState = data;
                return;
            }

            let hasUpdate = false;

            if (ROLE === 'debitur') {
                // Auto-reload jika status pengajuan berubah
                if (data.status && lastKnownState.status && data.status !== lastKnownState.status) {
                    hasUpdate = true;
                } else if (data.last_updated && lastKnownState.last_updated && data.last_updated !== lastKnownState.last_updated) {
                    hasUpdate = true;
                }

            } else if (ROLE === 'admin' || ROLE === 'marketing') {
                // Auto-reload jika ada submission baru atau data berubah
                if (data.total !== lastKnownState.total) {
                    hasUpdate = true;
                } else if (data.last_updated && lastKnownState.last_updated && data.last_updated !== lastKnownState.last_updated) {
                    hasUpdate = true;
                }

                // Juga update badge pending count langsung di DOM tanpa reload
                const pendingEl = document.getElementById('realtime-pending-count');
                if (pendingEl && data.pending_count !== undefined) {
                    pendingEl.textContent = data.pending_count;
                    pendingEl.style.display = data.pending_count > 0 ? 'inline' : 'none';
                }

            } else if (ROLE === 'pimpinan') {
                // Auto-reload jika jumlah yang perlu disetujui berubah
                if (data.need_approval !== lastKnownState.need_approval) {
                    hasUpdate = true;
                } else if (data.last_updated && lastKnownState.last_updated && data.last_updated !== lastKnownState.last_updated) {
                    hasUpdate = true;
                }

                // Update badge langsung
                const approvalEl = document.getElementById('realtime-approval-count');
                if (approvalEl && data.need_approval !== undefined) {
                    approvalEl.textContent = data.need_approval;
                    approvalEl.style.display = data.need_approval > 0 ? 'inline' : 'none';
                }
            }

            if (hasUpdate) {
                silentReload();
                return; // stop polling setelah reload
            }

            // Simpan state terbaru
            lastKnownState = data;

        } catch (err) {
            console.debug('[Realtime] Polling error:', err.message);
        }
    }

    // Mulai setelah halaman siap
    document.addEventListener('DOMContentLoaded', function () {
        // Tunggu 5 detik baru mulai poll (beri waktu halaman selesai render)
        setTimeout(() => {
            pollUpdates();
            setInterval(pollUpdates, POLL_INTERVAL);
        }, 5000);

        // Indikator Live — titik hijau kecil kiri bawah
        const dot = document.createElement('div');
        dot.innerHTML = `<span style="display:inline-block;width:8px;height:8px;background:#10B981;border-radius:50%;animation:pulse-dot 2s infinite;"></span><span style="font-size:10px;color:#94A3B8;margin-left:5px;">Live</span>`;
        dot.style.cssText = 'position:fixed;bottom:10px;left:15px;z-index:9998;display:flex;align-items:center;';
        document.body.appendChild(dot);
    });

})();
</script>
<style>
@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: 0.4; transform: scale(1.4); }
}
</style>

</body>
</html>
