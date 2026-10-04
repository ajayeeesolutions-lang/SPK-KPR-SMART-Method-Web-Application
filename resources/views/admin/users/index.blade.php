@extends('layouts.app')

@section('title', 'Kelola User & Role Access')
@section('page-title', 'Kelola User System (Admin, Manager, Nasabah)')

@section('content')
<div class="row g-3">
    <!-- User List -->
    <div class="col-12 col-lg-8">
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">Daftar Pengguna Sistem</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No. HP</th>
                            <th>Role Access</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                            <tr>
                                <td><div class="fw-bold text-dark">{{ $u->name }}</div></td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->phone ?? '-' }}</td>
                                <td>
                                    @if($u->role === 'admin')
                                        <span class="badge bg-primary text-uppercase">Admin</span>
                                    @elseif($u->role === 'marketing')
                                        <span class="badge bg-info text-uppercase">Marketing</span>
                                    @elseif($u->role === 'pimpinan')
                                        <span class="badge bg-success text-uppercase">Pimpinan</span>
                                    @else
                                        <span class="badge bg-secondary text-uppercase">Debitur</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="d-inline" >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" {{ $u->id === auth()->id() ? 'disabled' : '' }}><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Add User Form -->
    <div class="col-12 col-lg-4">
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-user-plus text-primary me-2"></i> Tambah User Baru</h6>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="email@citra.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor Handphone</label>
                    <input type="text" name="phone" class="form-control" placeholder="0812...">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Role Hak Akses</label>
                    <select name="role" class="form-select" required>
                        <option value="admin">Admin</option>
                        <option value="marketing">Marketing</option>
                        <option value="pimpinan">Pimpinan</option>
                        <option value="debitur">Calon Debitur</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 shadow-sm"><i class="fa-solid fa-save me-1"></i> Simpan User</button>
            </form>
        </div>
    </div>
</div>
@endsection

