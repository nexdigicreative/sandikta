@extends('layouts.app')
@section('title', 'Edit User - Perpus Sandikta')
@section('page-title', 'Edit User')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card-modern">
            <div class="card-header"><h6><i class="bi bi-pencil-square me-2 text-primary"></i>Edit User: {{ $user->name }}</h6></div>
            <div class="card-body" style="padding:32px">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:13px">NIS</label>
                        <input type="text" name="nis" value="{{ old('nis', $user->nis) }}" class="form-control form-control-modern @error('nis') is-invalid @enderror" required>
                        @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:13px">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control form-control-modern @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:13px">Kelas</label>
                        <input type="text" name="kelas" value="{{ old('kelas', $user->kelas) }}" class="form-control form-control-modern @error('kelas') is-invalid @enderror" required>
                        @error('kelas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;font-size:13px">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $user->tanggal_lahir?->format('Y-m-d')) }}" class="form-control form-control-modern @error('tanggal_lahir') is-invalid @enderror" required>
                        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-modern">Batal</a>
                        <button type="submit" class="btn btn-primary-modern"><i class="bi bi-check-lg me-1"></i>Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
