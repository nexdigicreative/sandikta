@extends('layouts.app')
@section('title', 'Kelola User - Perpus Sandikta')
@section('page-title', 'Kelola User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 style="font-weight:700;margin:0">Daftar User / Murid</h5>
        <p class="text-muted mb-0" style="font-size:13px">Kelola semua akun murid</p>
    </div>
    <div class="d-flex gap-2">
        <!-- Bulk Delete CSV -->
        @if(auth()->user()->isSuperadmin())
        <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
            <i class="bi bi-trash-fill me-1"></i><span class="d-none d-sm-inline">Hapus Masal</span>
        </button>
        @endif
        <!-- Import CSV -->
        <button class="btn btn-outline-modern" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-upload me-1"></i><span class="d-none d-sm-inline">Import CSV</span>
        </button>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary-modern">
            <i class="bi bi-plus-lg me-1"></i><span class="d-none d-sm-inline">Tambah User</span>
        </a>
    </div>
</div>

{{-- Bulk Action Bar (Floating/Hidden) --}}
@if(auth()->user()->isSuperadmin())
<div id="bulk-action-bar" class="card-modern mb-4 d-none animate-fadeInUp" style="background: #fff1f2; border-color: #fecdd3;">
    <div class="card-body py-2 d-flex justify-content-between align-items-center">
        <div class="text-danger fw-bold" style="font-size: 14px;">
            <i class="bi bi-check2-square me-2"></i><span id="selected-count">0</span> Anggota Terpilih
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">Batal</button>
            <form action="{{ route('admin.users.bulk-delete') }}" method="POST" id="bulk-delete-form">
                @csrf
                <div id="selected-ids-container"></div>
                <button type="button" class="btn btn-sm btn-danger" onclick="confirmBulkDelete()">Hapus Semua Terpilih</button>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Filter -->
<div class="card-modern mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-6">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-modern" placeholder="Cari nama, NIS, kelas...">
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-control form-control-modern">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status')=='active'?'selected':'' }}>Aktif</option>
                    <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-6 col-md-3"><button type="submit" class="btn btn-primary-modern w-100"><i class="bi bi-search me-1"></i>Filter</button></div>
        </form>
    </div>
</div>

{{-- Desktop Table --}}
<div class="card-modern d-none d-md-block">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        @if(auth()->user()->isSuperadmin())
                        <th style="width: 40px; text-align: center;"><input type="checkbox" id="check-all" class="form-check-input"></th>
                        @endif
                        <th>NIS</th><th>Nama</th><th>Kelas</th><th>Tgl Lahir</th><th>Status</th><th>Login Terakhir</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="user-row" data-id="{{ $user->id }}">
                        @if(auth()->user()->isSuperadmin())
                        <td class="text-center"><input type="checkbox" class="form-check-input user-checkbox" value="{{ $user->id }}" onchange="updateBulkBar()"></td>
                        @endif
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
                    <tr><td colspan="{{ auth()->user()->isSuperadmin() ? 8 : 7 }}" class="text-center py-4 text-muted">Belum ada user</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Mobile Card List --}}
<div class="d-md-none">
    @forelse($users as $user)
    <div class="card-modern mb-2 user-row-mobile" data-id="{{ $user->id }}">
        <div class="card-body" style="padding:14px 16px">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="d-flex gap-2 align-items-center">
                    @if(auth()->user()->isSuperadmin())
                    <input type="checkbox" class="form-check-input user-checkbox" value="{{ $user->id }}" onchange="updateBulkBar()">
                    @endif
                    <div style="min-width:0">
                        <div style="font-weight:700;font-size:14px">{{ $user->name }}</div>
                        <div style="font-size:12px;color:#64748b">NIS: {{ $user->nis }} • {{ $user->kelas }}</div>
                    </div>
                </div>
                @if($user->is_active)<span class="badge-modern badge-success">Aktif</span>@else<span class="badge-modern badge-danger">Nonaktif</span>@endif
            </div>
            <div style="font-size:11px;color:#94a3b8;margin-bottom:10px">
                <i class="bi bi-clock me-1"></i>{{ $user->last_login_at?->diffForHumans() ?? 'Belum login' }}
                @if($user->tanggal_lahir)<span class="mx-1">•</span><i class="bi bi-calendar me-1"></i>{{ $user->tanggal_lahir->format('d/m/Y') }}@endif
            </div>
            <div class="d-flex gap-1">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary flex-fill" style="border-radius:8px"><i class="bi bi-pencil me-1"></i>Edit</a>
                <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="d-inline">@csrf @method('PATCH')
                    <button class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" style="border-radius:8px;padding:4px 10px">
                        <i class="bi bi-{{ $user->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="d-inline">@csrf @method('PATCH')
                    <button class="btn btn-sm btn-outline-info" style="border-radius:8px;padding:4px 10px" onclick="return confirm('Reset password user ini?')"><i class="bi bi-key"></i></button>
                </form>
                @if(auth()->user()->isSuperadmin())
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" id="del-user-m-{{ $user->id }}">@csrf @method('DELETE')</form>
                <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;padding:4px 10px" onclick="confirmDelete('del-user-m-{{ $user->id }}')"><i class="bi bi-trash"></i></button>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="card-modern"><div class="card-body text-center text-muted py-4">Belum ada user</div></div>
    @endforelse
</div>

<div class="mt-3 pagination-wrapper">
    {{ $users->withQueryString()->links() }}
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
                        <i class="bi bi-info-circle me-2"></i>Format CSV: <strong>NIS, Nama, Kelas, Tanggal Lahir (YYYY-MM-DD)</strong>
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
@if(auth()->user()->isSuperadmin())
<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:20px;border:none">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9;padding:24px; background: #fff1f2;">
                <h5 class="modal-title text-danger" style="font-weight:700">Hapus User Masal (CSV)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.bulk-delete') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="padding:24px">
                    <div class="alert alert-danger mb-3" style="border-radius:12px;border:none;background:#fee2e2;color:#991b1b;font-size:13px">
                        <i class="bi bi-exclamation-triangle me-2"></i>Hati-hati! Semua NIS yang terdaftar di file CSV akan <strong>DIHAPUS PERMANEN</strong> dari sistem.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Upload File CSV NIS</label>
                        <input type="file" name="file" class="form-control form-control-modern" accept=".csv,.txt" required>
                        <small class="text-muted mt-1 d-block" style="font-size: 11px;">Hanya butuh 1 kolom berisi NIS saja.</small>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between" style="border:none;padding:0 24px 24px">
                    <a href="{{ route('admin.users.delete-template') }}" class="btn btn-outline-secondary" style="border-radius:12px;font-weight:600">
                        <i class="bi bi-download me-1"></i>Template CSV
                    </a>
                    <button type="submit" class="btn btn-danger" style="border-radius:12px;font-weight:600" onclick="return confirm('Yakin ingin menghapus semua user dalam file ini?')">
                        <i class="bi bi-trash-fill me-1"></i>Mulai Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    // Checkbox logic
    const checkAll = document.getElementById('check-all');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkBar = document.getElementById('bulk-action-bar');
    const selectedCountLabel = document.getElementById('selected-count');
    const idsContainer = document.getElementById('selected-ids-container');

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            userCheckboxes.forEach(cb => cb.checked = checkAll.checked);
            updateBulkBar();
        });
    }

    function updateBulkBar() {
        const checked = document.querySelectorAll('.user-checkbox:checked');
        const count = checked.length;
        
        if (count > 0) {
            bulkBar.classList.remove('d-none');
            selectedCountLabel.textContent = count;
            
            // Update hidden inputs
            idsContainer.innerHTML = '';
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_users[]';
                input.value = cb.value;
                idsContainer.appendChild(input);
            });
        } else {
            bulkBar.classList.add('d-none');
            if (checkAll) checkAll.checked = false;
        }
    }

    function deselectAll() {
        userCheckboxes.forEach(cb => cb.checked = false);
        if (checkAll) checkAll.checked = false;
        updateBulkBar();
    }

    function confirmBulkDelete() {
        const count = document.querySelectorAll('.user-checkbox:checked').length;
        Swal.fire({
            title: `Hapus ${count} User?`,
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('bulk-delete-form').submit();
            }
        });
    }
</script>
@endpush
@endsection
