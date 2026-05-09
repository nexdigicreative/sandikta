@extends('layouts.app')
@section('title', 'Kategori - Perpus Sandikta')
@section('page-title', 'Kelola Kategori')

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card-modern">
            <div class="card-header"><h6><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Kategori</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:13px">Nama Kategori</label>
                        <input type="text" name="name" class="form-control form-control-modern @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:13px">Deskripsi</label>
                        <textarea name="description" class="form-control form-control-modern" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary-modern w-100"><i class="bi bi-plus-lg me-1"></i>Tambah</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card-modern">
            <div class="card-header"><h6><i class="bi bi-bookmark-star me-2 text-warning"></i>Daftar Kategori</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead><tr><th>Nama</th><th>Deskripsi</th><th>Jumlah eBook</th><th>Aksi</th></tr></thead>
                        <tbody>
                            @forelse($categories as $cat)
                            <tr>
                                <td><strong>{{ $cat->name }}</strong></td>
                                <td style="font-size:13px;color:#64748b">{{ Str::limit($cat->description, 40) }}</td>
                                <td><span class="badge-modern badge-info">{{ $cat->ebooks_count }} eBook</span></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline-primary" style="border-radius:8px;padding:4px 10px" data-bs-toggle="modal" data-bs-target="#editCat{{ $cat->id }}"><i class="bi bi-pencil"></i></button>
                                        @if($cat->ebooks_count == 0)
                                        <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" id="del-cat-{{ $cat->id }}">@csrf @method('DELETE')</form>
                                        <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;padding:4px 10px" onclick="confirmDelete('del-cat-{{ $cat->id }}')"><i class="bi bi-trash"></i></button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <!-- Edit Modal -->
                            <div class="modal fade" id="editCat{{ $cat->id }}" tabindex="-1">
                                <div class="modal-dialog"><div class="modal-content" style="border-radius:20px;border:none">
                                    <div class="modal-header" style="padding:24px"><h5 class="modal-title" style="font-weight:700">Edit Kategori</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                    <form method="POST" action="{{ route('admin.categories.update', $cat) }}">@csrf @method('PUT')
                                        <div class="modal-body" style="padding:0 24px">
                                            <div class="mb-3"><label class="form-label" style="font-weight:600;font-size:13px">Nama</label><input type="text" name="name" value="{{ $cat->name }}" class="form-control form-control-modern" required></div>
                                            <div class="mb-3"><label class="form-label" style="font-weight:600;font-size:13px">Deskripsi</label><textarea name="description" class="form-control form-control-modern" rows="2">{{ $cat->description }}</textarea></div>
                                        </div>
                                        <div class="modal-footer" style="border:none;padding:0 24px 24px"><button type="submit" class="btn btn-primary-modern">Perbarui</button></div>
                                    </form>
                                </div></div>
                            </div>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada kategori</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-3">{{ $categories->links() }}</div>
    </div>
</div>
@endsection
