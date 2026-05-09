@extends('layouts.app')
@section('title', 'Tambah Admin - Perpus Sandikta')
@section('page-title', 'Tambah Admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card-modern">
            <div class="card-header"><h6><i class="bi bi-person-plus me-2 text-primary"></i>Form Tambah Admin</h6></div>
            <div class="card-body" style="padding:32px">
                <form method="POST" action="{{ route('superadmin.admins.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:13px">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-modern @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:13px">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-modern @error('email') is-invalid @enderror" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:13px">Password</label>
                        <input type="password" name="password" class="form-control form-control-modern @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;font-size:13px">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control form-control-modern" required>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('superadmin.admins.index') }}" class="btn btn-outline-modern">Batal</a>
                        <button type="submit" class="btn btn-primary-modern"><i class="bi bi-check-lg me-1"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
