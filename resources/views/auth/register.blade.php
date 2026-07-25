@extends('layouts.auth')

@section('title', 'Registrasi Nasabah')

@section('content')
<h5 class="fw-bold mb-3 text-dark text-center">Registrasi Calon Nasabah</h5>

@if($errors->any())
    <div class="alert alert-danger p-2 small rounded-3 mb-3">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('register') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label small fw-semibold text-secondary">Nama Lengkap (Sesuai KTP)</label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-custom" placeholder="Nama Lengkap" required autofocus>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold text-secondary">Email Address</label>
        <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-custom" placeholder="nama@domain.com" required>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold text-secondary">Nomor Handphone / WA</label>
        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control form-control-custom" placeholder="081234567890" required>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold text-secondary">Password</label>
        <input type="password" name="password" class="form-control form-control-custom" placeholder="Minimal 8 karakter" required>
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control form-control-custom" placeholder="Ulangi password" required>
    </div>

    <button type="submit" class="btn btn-bank w-100 mb-3 shadow-sm">
        <i class="fa-solid fa-user-plus me-2"></i> Daftar Akun KPR
    </button>
</form>

<div class="text-center mt-3 pt-3 border-top">
    <div class="small text-muted mb-2">Sudah memiliki akun?</div>
    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm w-100 rounded-3">
        Kembali ke Login
    </a>
</div>
@endsection
