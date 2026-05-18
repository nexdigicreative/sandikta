@extends('layouts.app')
@section('title', 'Kelola Admin - Perpus Sandikta')
@section('page-title', 'Kelola Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h5 style="font-weight:700;margin:0">Daftar Admin</h5></div>
    <a href="{{ route('superadmin.admins.create') }}" class="btn btn-primary-modern"><i class="bi bi-plus-lg me-1"></i>Tambah Admin</a>
</div>

{{-- Desktop Table --}}
<div class="card-modern d-none d-md-block">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern">
                <thead><tr><th>Nama</th><th>Email</th><th>Status</th><th>Login Terakhir</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($admins as $admin)
                    <tr class="admin-row" data-id="{{ $admin->id }}">
                        <td><strong>{{ $admin->name }}</strong></td>
                        <td>{{ $admin->email }}</td>
                        <td>@if($admin->is_active)<span class="badge-modern badge-success">Aktif</span>@else<span class="badge-modern badge-danger">Nonaktif</span>@endif</td>
                        <td style="font-size:12px;color:#94a3b8">{{ $admin->last_login_at?->diffForHumans() ?? 'Belum login' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('superadmin.admins.edit', $admin) }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;padding:4px 10px"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('superadmin.admins.toggle', $admin) }}" class="d-inline">@csrf @method('PATCH')
                                    <button class="btn btn-sm {{ $admin->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" style="border-radius:8px;padding:4px 10px"><i class="bi bi-{{ $admin->is_active ? 'pause-circle' : 'play-circle' }}"></i></button>
                                </form>
                                <form method="POST" action="{{ route('superadmin.admins.destroy', $admin) }}" id="del-admin-{{ $admin->id }}">@csrf @method('DELETE')</form>
                                <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;padding:4px 10px" onclick="confirmDelete('del-admin-{{ $admin->id }}')"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada admin</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Mobile Card List --}}
<div class="d-md-none">
    @forelse($admins as $admin)
    <div class="card-modern mb-2">
        <div class="card-body" style="padding:14px 16px">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div style="min-width:0">
                    <div style="font-weight:700;font-size:14px">{{ $admin->name }}</div>
                    <div style="font-size:12px;color:#94a3b8">{{ $admin->email }}</div>
                </div>
                @if($admin->is_active)<span class="badge-modern badge-success">Aktif</span>@else<span class="badge-modern badge-danger">Nonaktif</span>@endif
            </div>
            <div style="font-size:11px;color:#94a3b8;margin-bottom:10px">
                <i class="bi bi-clock me-1"></i>{{ $admin->last_login_at?->diffForHumans() ?? 'Belum login' }}
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('superadmin.admins.edit', $admin) }}" class="btn btn-sm btn-outline-primary flex-fill" style="border-radius:8px"><i class="bi bi-pencil me-1"></i>Edit</a>
                <form method="POST" action="{{ route('superadmin.admins.toggle', $admin) }}" class="flex-fill">@csrf @method('PATCH')
                    <button class="btn btn-sm {{ $admin->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} w-100" style="border-radius:8px">
                        <i class="bi bi-{{ $admin->is_active ? 'pause-circle' : 'play-circle' }} me-1"></i>{{ $admin->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('superadmin.admins.destroy', $admin) }}" id="del-admin-m-{{ $admin->id }}">@csrf @method('DELETE')</form>
                <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;padding:4px 10px" onclick="confirmDelete('del-admin-m-{{ $admin->id }}')"><i class="bi bi-trash"></i></button>
            </div>
        </div>
    </div>
    @empty
    <div class="card-modern"><div class="card-body text-center text-muted py-4">Belum ada admin</div></div>
    @endforelse
</div>

<div class="mt-3 pagination-wrapper">
    {{ $admins->links() }}
</div>

@endsection
