@extends('layouts.app')
@section('title', 'Kelola eBook - Perpus Sandikta')
@section('page-title', 'Kelola eBook')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
    <div>
        <h5 style="font-weight:700;margin:0">Daftar eBook</h5>
        <p class="text-muted mb-0" style="font-size:13px">Kelola koleksi eBook perpustakaan</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.ebooks.bulk') }}" class="btn btn-success-modern"><i class="bi bi-cloud-arrow-up me-1"></i>Bulk Upload (Multi)</a>
        <a href="{{ route('admin.ebooks.create') }}" class="btn btn-primary-modern"><i class="bi bi-plus-lg me-1"></i>Upload eBook</a>
    </div>
</div>

<div class="card-modern mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-9">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-modern" placeholder="Cari judul, penulis...">
            </div>
            <div class="col-md-3"><button type="submit" class="btn btn-primary-modern w-100"><i class="bi bi-search me-1"></i>Cari</button></div>
        </form>
    </div>
</div>

<div class="card-modern">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr><th>Cover</th><th>Judul</th><th>Penulis</th><th>Kategori</th><th>Kelas</th><th>Dibaca</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($ebooks as $ebook)
                    <tr>
                        <td>
                            <div style="width:40px;height:52px;border-radius:8px;background:linear-gradient(135deg,#dbeafe,#bfdbfe);display:flex;align-items:center;justify-content:center;overflow:hidden">
                                @if($ebook->cover_image)
                                <img src="{{ asset('storage/'.$ebook->cover_image) }}" style="width:100%;height:100%;object-fit:cover" alt="">
                                @else
                                <i class="bi bi-file-pdf" style="color:#1e40af"></i>
                                @endif
                            </div>
                        </td>
                        <td><strong>{{ Str::limit($ebook->title, 35) }}</strong><br><small class="text-muted">{{ $ebook->formatted_file_size }}</small></td>
                        <td>{{ $ebook->author }}</td>
                        <td><span class="badge-modern badge-info">{{ $ebook->category?->name }}</span></td>
                        <td>{{ $ebook->kelas_tujuan ?? '-' }}</td>
                        <td>{{ $ebook->view_count }}x</td>
                        <td>
                            @if($ebook->is_active)<span class="badge-modern badge-success">Aktif</span>
                            @else<span class="badge-modern badge-danger">Nonaktif</span>@endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.ebooks.edit', $ebook) }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;padding:4px 10px"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.ebooks.toggle', $ebook) }}" class="d-inline">@csrf @method('PATCH')
                                    <button class="btn btn-sm {{ $ebook->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" style="border-radius:8px;padding:4px 10px">
                                        <i class="bi bi-{{ $ebook->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.ebooks.destroy', $ebook) }}" id="del-ebook-{{ $ebook->id }}">@csrf @method('DELETE')</form>
                                <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;padding:4px 10px" onclick="confirmDelete('del-ebook-{{ $ebook->id }}')"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada eBook</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $ebooks->withQueryString()->links() }}</div>
@endsection
