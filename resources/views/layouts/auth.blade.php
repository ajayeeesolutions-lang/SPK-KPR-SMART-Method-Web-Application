<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') - SPK Kelayakan KPR SMART Bank Sejahtera</title>
    
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
    <meta name="theme-color" content="#0F172A">
    <link rel="apple-touch-icon" href="{{ asset('pwa-icon-192.png') }}">

    <style>
        :root {
            --primary-blue: #2563EB;
            --primary-hover: #1D4ED8;
            --dark-navy: #0F172A;
            --surface-glass: rgba(255, 255, 255, 0.96);
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background: radial-gradient(circle at 50% 20%, #1E3A8A 0%, #0F172A 70%, #090D16 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.25rem;
            margin: 0;
            position: relative;
            overflow-y: auto;
        }

        /* Subtle Ambient Glow Background Orbs */
        body::before {
            content: '';
            position: fixed;
            top: 10%;
            left: 20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.25) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            z-index: 0;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: 10%;
            right: 20%;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.2) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            z-index: 0;
            pointer-events: none;
        }

        .auth-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            margin: auto;
        }

        .auth-card {
            background: var(--surface-glass);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-radius: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.2) inset;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .auth-header {
            background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
            padding: 1.75rem 1.75rem 1.25rem 1.75rem;
            text-align: center;
            border-bottom: 1px solid #E2E8F0;
        }

        .auth-logo-icon {
            width: 58px;
            height: 58px;
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            color: #FFFFFF;
            border-radius: 1.1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
            margin-bottom: 0.85rem;
        }

        .form-control-custom {
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            border: 1.5px solid #E2E8F0;
            background-color: #FFFFFF;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            border-color: #2563EB;
            background-color: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .input-group-text-custom {
            border-radius: 0.75rem 0 0 0.75rem;
            background-color: #F8FAFC;
            border: 1.5px solid #E2E8F0;
            border-end-0: none;
            color: #64748B;
            padding-left: 1rem;
            padding-right: 0.75rem;
        }

        .btn-bank {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            color: #FFFFFF;
            font-weight: 700;
            padding: 0.85rem 1.25rem;
            border-radius: 0.75rem;
            border: none;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.35);
            transition: all 0.25s ease;
        }

        .btn-bank:hover {
            background: linear-gradient(135deg, #1D4ED8 0%, #1E40AF 100%);
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 14px 24px -5px rgba(37, 99, 235, 0.45);
        }

        .demo-chip {
            cursor: pointer;
            padding: 0.4rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s ease;
            user-select: none;
        }

        .demo-chip:hover {
            transform: scale(1.03);
            filter: brightness(1.1);
        }

        .auth-footer-text {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            text-align: center;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo-icon" style="background: white; padding: 5px;">
                <img src="{{ asset('pwa-icon-192.png') }}" alt="Logo App" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <h4 class="fw-extrabold text-dark mb-1" style="letter-spacing: -0.5px;">SPK Kelayakan KPR</h4>
            <div class="text-secondary small fw-medium">Metode SMART (Simple Multi Attribute Rating Technique)</div>
        </div>

        <div class="p-4 p-sm-4">
            @yield('content')
        </div>
    </div>

    <div class="auth-footer-text">
        &copy; {{ date('Y') }} <strong>Bank KPR Sejahtera Indonesia</strong>. All rights reserved.
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
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
@yield('scripts')
</body>
</html>
