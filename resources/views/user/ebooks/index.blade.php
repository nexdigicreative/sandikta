@extends('layouts.app')
@section('title', 'Koleksi eBook - Perpus Sandikta')
@section('page-title', 'Koleksi eBook')

@section('content')
<!-- Search & Filter -->
<div class="card-modern mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label" style="font-size:13px;font-weight:600">Cari eBook</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-modern" placeholder="Judul, penulis...">
            </div>
            <div class="col-md-3">
                <label class="form-label" style="font-size:13px;font-weight:600">Kategori</label>
                <select name="category" class="form-control form-control-modern">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:13px;font-weight:600">Kelas</label>
                <input type="text" name="kelas" value="{{ request('kelas') }}" class="form-control form-control-modern" placeholder="Kelas">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary-modern w-100"><i class="bi bi-search me-1"></i>Cari</button>
            </div>
        </form>
    </div>
</div>

<!-- eBooks Grid -->
<div class="row g-4">
    @forelse($ebooks as $ebook)
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card-modern h-100" style="transition:all .3s" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 15px 35px rgba(30,64,175,0.12)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            <div style="aspect-ratio:3/4;width:100%;background:linear-gradient(135deg,#1e40af,#3b82f6);position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center">
                @if($ebook->cover_image)
                <img src="{{ asset('storage/'.$ebook->cover_image) }}" style="width:100%;height:100%;object-fit:cover" alt="">
                @else
                <div style="display:flex;align-items:center;justify-content:center;height:100%">
                    <i class="bi bi-book" style="font-size:56px;color:rgba(255,255,255,0.2)"></i>
                </div>
                @endif
                <div style="position:absolute;top:12px;right:12px">
                    <span class="badge-modern badge-info">{{ $ebook->category?->name }}</span>
                </div>
            </div>
            <div class="card-body d-flex flex-column" style="padding:20px">
                <h6 style="font-weight:700;font-size:15px;margin-bottom:6px">{{ Str::limit($ebook->title, 40) }}</h6>
                <p style="font-size:13px;color:#64748b;margin-bottom:4px"><i class="bi bi-person me-1"></i>{{ $ebook->author }}</p>
                @if($ebook->kelas_tujuan)
                <p style="font-size:12px;color:#94a3b8;margin-bottom:12px"><i class="bi bi-mortarboard me-1"></i>{{ $ebook->kelas_tujuan }}</p>
                @endif
                <div class="mt-auto d-flex gap-2">
                    <a href="{{ route('ebooks.show', $ebook) }}" class="btn btn-outline-modern flex-grow-1" style="font-size:13px;padding:8px">Detail</a>
                    <a href="{{ route('ebooks.read', $ebook) }}" class="btn btn-primary-modern flex-grow-1" style="font-size:13px;padding:8px"><i class="bi bi-book me-1"></i>Baca</a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card-modern">
            <div class="card-body text-center py-5">
                <i class="bi bi-journal-x" style="font-size:64px;color:#cbd5e1"></i>
                <h5 class="mt-3" style="color:#64748b">Tidak ada eBook ditemukan</h5>
                <p class="text-muted">Coba ubah kata kunci pencarian atau filter Anda</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-4 d-flex justify-content-center pagination-wrapper">
    {{ $ebooks->withQueryString()->links() }}
</div>
@endsection
