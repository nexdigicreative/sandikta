@extends('layouts.app')
@section('title', 'Tambah User - Perpus Sandikta')
@section('page-title', 'Tambah User')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card-modern">
            <div class="card-header"><h6><i class="bi bi-person-plus me-2 text-primary"></i>Form Tambah User</h6></div>
            <div class="card-body" style="padding:32px">
                <div class="alert alert-info" style="border-radius:12px;border:none;background:#dbeafe;color:#1e40af;font-size:13px">
                    <i class="bi bi-info-circle me-2"></i>Password default: tanggal lahir (ddmmyyyy). User wajib mengubahnya saat login pertama.
                </div>
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:13px">NIS <span class="text-danger">*</span></label>
                        <input type="text" name="nis" value="{{ old('nis') }}" class="form-control form-control-modern @error('nis') is-invalid @enderror" required>
                        @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:13px">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-modern @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:13px">Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="kelas" value="{{ old('kelas') }}" class="form-control form-control-modern @error('kelas') is-invalid @enderror" placeholder="cth: XII RPL 1" required>
                        @error('kelas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;font-size:13px">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="form-control form-control-modern @error('tanggal_lahir') is-invalid @enderror" required>
                        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-modern">Batal</a>
                        <button type="submit" class="btn btn-primary-modern"><i class="bi bi-check-lg me-1"></i>Simpan User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
