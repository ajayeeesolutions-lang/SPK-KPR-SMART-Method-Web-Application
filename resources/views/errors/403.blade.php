@extends('layouts.auth')

@section('title', '403 Forbidden')

@section('content')
<div class="text-center py-4">
    <div class="mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle" style="width: 80px; height: 80px;">
            <i class="fa-solid fa-lock fs-1"></i>
        </div>
    </div>
    <h1 class="fw-extrabold text-dark mb-2" style="font-size: 3rem;">403</h1>
    <h4 class="fw-bold text-dark mb-3">Akses Ditolak</h4>
    <p class="text-muted mb-4">Maaf, Anda tidak memiliki hak akses (permission) untuk melihat halaman atau melakukan aksi ini.</p>
    
    <a href="{{ url('/') }}" class="btn btn-bank w-100 rounded-pill shadow-sm">
        <i class="fa-solid fa-house me-2"></i> Kembali ke Beranda
    </a>
</div>
@endsection
