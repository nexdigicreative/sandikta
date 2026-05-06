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
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12">
        <div class="card-modern h-100" style="transition:all .3s" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 15px 35px rgba(30,64,175,0.12)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            <div style="height:280px;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#0f172a;padding:15px;border-bottom:1px solid #f1f5f9">
                @if($ebook->cover_image)
                <!-- Blurred background for depth -->
                <div style="position:absolute;top:0;left:0;right:0;bottom:0;background-image:url('{{ asset('storage/'.$ebook->cover_image) }}');background-size:cover;background-position:center;filter:blur(12px) opacity(0.25);transform:scale(1.15)"></div>
                <!-- Main sharp cover image -->
                <img src="{{ asset('storage/'.$ebook->cover_image) }}" style="max-width:100%;max-height:100%;object-fit:contain;position:relative;z-index:1;box-shadow:0 6px 16px rgba(0,0,0,0.35);border-radius:4px" alt="">
                @else
                <!-- Beautiful CSS Book Cover Fallback with Book Title -->
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;width:100%;padding:20px;text-align:center;color:white;background:linear-gradient(135deg, #1e3a8a, #3b82f6)">
                    <i class="bi bi-journal-text mb-2" style="font-size:32px;opacity:0.8"></i>
                    <span style="font-size:12px;font-weight:700;line-height:1.25;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;text-transform:capitalize">{{ $ebook->title }}</span>
                </div>
                @endif
                <div style="position:absolute;top:12px;right:12px;z-index:10">
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

<div class="mt-4 d-flex justify-content-center">{{ $ebooks->withQueryString()->links() }}</div>
@endsection
