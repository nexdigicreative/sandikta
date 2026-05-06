@extends('layouts.app')
@section('title', 'Kelola User - Perpus Sandikta')
@section('page-title', 'Kelola User')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
    <div>
        <h5 style="font-weight:700;margin:0">Daftar User / Murid</h5>
        <p class="text-muted mb-0" style="font-size:13px">Kelola semua akun murid</p>
    </div>
    <div class="d-flex gap-2 flex-wrap w-100 w-sm-auto">
        <!-- Import CSV -->
        <button class="btn btn-outline-modern flex-grow-1 flex-sm-grow-0" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-upload me-1"></i>Import CSV
        </button>
        <!-- Bulk Delete -->
        <button class="btn btn-outline-danger flex-grow-1 flex-sm-grow-0" style="border-radius:12px;font-weight:600" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
            <i class="bi bi-trash me-1"></i>Hapus Massal
        </button>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary-modern flex-grow-1 flex-sm-grow-0">
            <i class="bi bi-plus-lg me-1"></i>Tambah User
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card-modern mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-6">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-modern" placeholder="Cari nama, NIS, kelas...">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control form-control-modern">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status')=='active'?'selected':'' }}>Aktif</option>
                    <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-3"><button type="submit" class="btn btn-primary-modern w-100"><i class="bi bi-search me-1"></i>Filter</button></div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card-modern">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr><th>NIS</th><th>Nama</th><th>Kelas</th><th>Tgl Lahir</th><th>Status</th><th>Login Terakhir</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td><strong>{{ $user->nis }}</strong></td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->kelas }}</td>
                        <td>{{ $user->tanggal_lahir?->format('d/m/Y') }}</td>
                        <td>
                            @if($user->is_active)
                            <span class="badge-modern badge-success">Aktif</span>
                            @else
                            <span class="badge-modern badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#94a3b8">{{ $user->last_login_at?->diffForHumans() ?? 'Belum login' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;padding:4px 10px" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="d-inline">@csrf @method('PATCH')
                                    <button class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" style="border-radius:8px;padding:4px 10px" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi bi-{{ $user->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="d-inline">@csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-info" style="border-radius:8px;padding:4px 10px" title="Reset Password" onclick="return confirm('Reset password user ini?')"><i class="bi bi-key"></i></button>
                                </form>
                                @if(auth()->user()->isSuperadmin())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" id="del-user-{{ $user->id }}">@csrf @method('DELETE')</form>
                                <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;padding:4px 10px" onclick="confirmDelete('del-user-{{ $user->id }}')"><i class="bi bi-trash"></i></button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada user</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-top-0 px-4 py-3">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
            <div class="text-secondary" style="font-size: 13px;">
                Menampilkan <strong>{{ $users->firstItem() ?? 0 }}</strong> sampai <strong>{{ $users->lastItem() ?? 0 }}</strong> dari <strong>{{ $users->total() }}</strong> user
            </div>
            <div class="pagination-modern">
                {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:20px;border:none">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9;padding:24px">
                <h5 class="modal-title" style="font-weight:700">Import User dari CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="padding:24px">
                    <div class="alert alert-info mb-3" style="border-radius:12px;border:none;background:#dbeafe;color:#1e40af;font-size:13px">
                        <i class="bi bi-info-circle me-2"></i>Format CSV: <strong>NIS, Nama Lengkap, Kelas, Tanggal Lahir (DD-MM-YYYY atau DD/MM/YYYY)</strong>
                    </div>
                    <input type="file" name="file" class="form-control form-control-modern" accept=".csv,.txt" required>
                </div>
                <div class="modal-footer d-flex justify-content-between" style="border:none;padding:0 24px 24px">
                    <a href="{{ route('admin.users.template') }}" class="btn btn-outline-primary" style="border-radius:12px;font-weight:600">
                        <i class="bi bi-download me-1"></i>Template CSV
                    </a>
                    <button type="submit" class="btn btn-primary-modern"><i class="bi bi-upload me-1"></i>Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:20px;border:none">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9;padding:24px">
                <h5 class="modal-title text-danger" style="font-weight:700">Hapus Massal User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.bulk-delete') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="padding:24px">
                    <div class="alert alert-warning mb-3" style="border-radius:12px;border:none;background:#fff7ed;color:#9a3412;font-size:13px">
                        <i class="bi bi-exclamation-triangle me-2"></i><strong>Peringatan!</strong> Semua user yang NIS-nya terdaftar di file CSV akan dihapus secara permanen.
                    </div>
                    <p class="text-muted mb-2" style="font-size:13px">Upload file CSV yang berisi daftar <strong>NIS</strong> (Kolom pertama).</p>
                    <input type="file" name="file" class="form-control form-control-modern" accept=".csv,.txt" required>
                </div>
                <div class="modal-footer d-flex justify-content-end" style="border:none;padding:0 24px 24px">
                    <button type="button" class="btn btn-outline-secondary me-2" style="border-radius:12px;font-weight:600" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger" style="border-radius:12px;font-weight:600;padding:10px 24px" onclick="return confirm('Apakah Anda yakin ingin menghapus massal user tersebut?')">
                        <i class="bi bi-trash me-1"></i>Hapus Massal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
