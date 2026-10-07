<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') - SPK Kelayakan KPR SMART</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- PWA Settings -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#F4F6F9">
    <link rel="apple-touch-icon" href="{{ asset('pwa-icon-192.png') }}">

    <style>
        :root {
            --primary-blue: #2563EB;
            --primary-hover: #1D4ED8;
            --dark-navy: #0F172A;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background-color: #F4F6F9; /* Light greyish blue background */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 2rem 1rem;
        }

        .auth-wrapper {
            background: #FFFFFF;
            border-radius: 1.25rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: row;
            overflow: hidden;
            width: 100%;
            max-width: 950px;
            min-height: 600px;
        }

        /* Left Side (Form) */
        .auth-form-side {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-header-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .auth-logo-icon {
            width: 45px;
            height: 45px;
            border-radius: 0.5rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .auth-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: #111827;
            margin: 0;
            line-height: 1.2;
        }

        .auth-subtitle {
            font-size: 0.75rem;
            color: #6B7280;
            margin: 0;
        }

        /* Form Customizations */
        .form-control-custom {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            border-radius: 0.5rem;
            border: 1px solid #D1D5DB;
        }

        .form-control-custom:focus {
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            border-color: var(--primary-blue);
        }

        .input-group-text-custom {
            background: #F9FAFB;
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem 0 0 0.5rem;
            color: #6B7280;
        }

        .btn-primary-custom {
            background: var(--primary-blue);
            border: none;
            padding: 0.8rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-primary-custom:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        /* Right Side (Image Cover) */
        .auth-image-side {
            flex: 1;
            background: url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&q=80&w=1000') center/cover no-repeat;
            position: relative;
            display: none;
        }

        @media (min-width: 992px) {
            .auth-image-side {
                display: block; /* Show only on desktop */
            }
        }

        .auth-image-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.6) 60%, transparent 100%);
            padding: 3rem;
            color: #FFFFFF;
        }

        .badge-kpr {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 0.4em 0.8em;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            border-radius: 4px;
            margin-bottom: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .auth-image-title {
            font-size: 2.25rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1rem;
        }

        .auth-image-desc {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .auth-image-footer {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .auth-image-footer i {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem;
            border-radius: 50%;
        }

        .auth-footer-text {
            color: #9CA3AF;
            font-size: 0.8rem;
            text-align: center;
            margin-top: 1.5rem;
            width: 100%;
        }

        /* Demo Buttons Wrapper */
        .demo-buttons-wrapper {
            background: #F9FAFB;
            border: 1px dashed #D1D5DB;
            border-radius: 0.75rem;
            padding: 1rem;
        }

        .demo-chip {
            padding: 0.35rem 0.6rem;
            border-radius: 0.4rem;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s ease;
            user-select: none;
            color: white;
            display: inline-block;
            margin: 2px;
            text-decoration: none;
            border: none;
        }
        .demo-chip:hover {
            transform: scale(1.05);
            color: white;
            opacity: 0.9;
        }
    </style>
</head>
<body>

<div class="w-100 d-flex flex-column align-items-center px-3">
    <div class="auth-wrapper w-100">
        <!-- Kiri: Form -->
        <div class="auth-form-side">
            <div class="auth-header-logo">
                <div class="auth-logo-icon">
                    <img src="{{ asset('pwa-icon-192.png') }}" alt="Logo App" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div>
                    <div class="auth-title">SPK Kelayakan KPR</div>
                    <div class="auth-subtitle">Sistem Pendukung Keputusan Metode SMART</div>
                </div>
            </div>

            @yield('content')
        </div>

        <!-- Kanan: Gambar -->
        <div class="auth-image-side">
            <div class="auth-image-overlay">
                <div class="badge-kpr"><i class="fa-solid fa-house-chimney"></i> SOLUSI KPR LEBIH CERDAS</div>
                <h1 class="auth-image-title">Wujudkan rumah impian, mulai dari sini.</h1>
                <p class="auth-image-desc">Kelola perjalanan pengajuan KPR dengan proses yang transparan, terukur, dan terpercaya.</p>

                <div class="auth-image-footer">
                    <i class="fa-solid fa-shield-halved"></i> Penilaian transparan dengan metode SMART
                </div>
            </div>
        </div>
    </div>

    <div class="auth-footer-text">
        &copy; {{ date('Y') }} <strong>SPK Kelayakan KPR SMART</strong>. Semua hak dilindungi.
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>