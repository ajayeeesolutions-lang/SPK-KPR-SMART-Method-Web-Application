@extends('layouts.auth')

@section('title', 'Login Sistem')

@section('content')
<h5 class="fw-bold mb-3 text-dark text-center">Silakan Masuk</h5>

@if($errors->any())
    <div class="alert alert-danger p-2 px-3 small rounded-3 mb-3 border-0 shadow-sm">
        <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $errors->first() }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success p-2 px-3 small rounded-3 mb-3 border-0 shadow-sm">
        <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
    </div>
@endif

<form action="{{ route('login') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label small fw-semibold text-secondary mb-1">Email Address</label>
        <div class="input-group">
            <span class="input-group-text input-group-text-custom"><i class="fa-solid fa-envelope"></i></span>
            <input type="email" id="emailInput" name="email" value="{{ old('email') }}" class="form-control form-control-custom border-start-0" placeholder="nama@bank.com" required autofocus>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold text-secondary mb-1">Password</label>
        <div class="input-group">
            <span class="input-group-text input-group-text-custom"><i class="fa-solid fa-lock"></i></span>
            <input type="password" id="passwordInput" name="password" class="form-control form-control-custom border-start-0" placeholder="••••••••" required>
        </div>
    </div>

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div class="form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="remember">
            <label class="form-check-label small text-secondary" for="remember">Ingat Saya</label>
        </div>
    </div>

    <button type="submit" class="btn btn-bank w-100 mb-3">
        <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk Sekarang
    </button>
</form>

<div class="text-center pt-2 border-top">
    <div class="small text-muted mb-2">Belum memiliki akun calon nasabah?</div>
    <a href="{{ route('register') }}" class="btn btn-light border btn-sm w-100 rounded-3 text-secondary fw-semibold py-2">
        Registrasi Nasabah Baru
    </a>
</div>

<!-- 1-Click Interactive Demo Accounts -->
<div class="mt-4 p-3 bg-light rounded-4 border text-center">
    <small class="text-muted d-block fw-bold mb-2">💡 Klik Akun Demo di Bawah Ini:</small>
    <div class="d-flex justify-content-center gap-1 flex-wrap">
        <span class="demo-chip bg-primary text-white shadow-sm" onclick="fillDemo('admin@bank.com', 'password')">
            <i class="fa-solid fa-user-shield me-1"></i> Admin
        </span>
        <span class="demo-chip bg-success text-white shadow-sm" onclick="fillDemo('manager@bank.com', 'password')">
            <i class="fa-solid fa-user-check me-1"></i> Manager
        </span>
        <span class="demo-chip bg-dark text-white shadow-sm" onclick="fillDemo('nasabah@bank.com', 'password')">
            <i class="fa-solid fa-user-tie me-1"></i> Nasabah
        </span>
    </div>
    <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">Password default: <code>password</code></small>
</div>
@endsection

@section('scripts')
<script>
    function fillDemo(email, password) {
        document.getElementById('emailInput').value = email;
        document.getElementById('passwordInput').value = password;
    }
</script>
@endsection
