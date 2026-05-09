@extends('layouts.app')
@section('title', 'Edit Admin - Perpus Sandikta')
@section('page-title', 'Edit Admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card-modern">
            <div class="card-header"><h6><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Admin: {{ $admin->name }}</h6></div>
            <div class="card-body" style="padding:32px">
                <form method="POST" action="{{ route('superadmin.admins.update', $admin) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:13px">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $admin->name) }}" class="form-control form-control-modern" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:13px">Email</label>
                        <input type="email" name="email" value="{{ old('email', $admin->email) }}" class="form-control form-control-modern" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:13px">Password Baru <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
                        <input type="password" name="password" class="form-control form-control-modern">
                    </div>
                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;font-size:13px">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control form-control-modern">
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('superadmin.admins.index') }}" class="btn btn-outline-modern">Batal</a>
                        <button type="submit" class="btn btn-primary-modern"><i class="bi bi-check-lg me-1"></i>Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
