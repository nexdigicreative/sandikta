@extends('layouts.app')
@section('title', $ebook->title . ' - Perpus Sandikta')
@section('page-title', 'Detail eBook')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-modern animate-fadeInUp delay-1" style="overflow:hidden">
            <div style="height:320px;background:linear-gradient(135deg,#1e40af,#3b82f6);display:flex;align-items:center;justify-content:center">
                @if($ebook->cover_image)
                <img src="{{ asset('storage/'.$ebook->cover_image) }}" style="width:100%;height:100%;object-fit:cover" alt="">
                @else
                <i class="bi bi-book" style="font-size:80px;color:rgba(255,255,255,0.2)"></i>
                @endif
            </div>
            <div class="card-body text-center">
                <a href="{{ route('ebooks.read', $ebook) }}" class="btn btn-primary-modern w-100" style="padding:14px;font-size:16px">
                    <i class="bi bi-book-half me-2"></i>Baca Sekarang
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-modern animate-fadeInUp delay-2">
            <div class="card-body" style="padding:32px">
                <span class="badge-modern badge-info mb-3">{{ $ebook->category?->name }}</span>
                <h3 style="font-weight:800;margin-bottom:8px">{{ $ebook->title }}</h3>
                <p style="color:#64748b;font-size:15px;margin-bottom:24px">oleh <strong>{{ $ebook->author }}</strong></p>
                
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div style="padding:16px;background:#f8fafc;border-radius:12px;text-align:center">
                            <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase">Tahun</div>
                            <div style="font-weight:700;font-size:18px;margin-top:4px">{{ $ebook->year ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="padding:16px;background:#f8fafc;border-radius:12px;text-align:center">
                            <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase">Dibaca</div>
                            <div style="font-weight:700;font-size:18px;margin-top:4px">{{ $ebook->view_count }}x</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="padding:16px;background:#f8fafc;border-radius:12px;text-align:center">
                            <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase">Ukuran</div>
                            <div style="font-weight:700;font-size:18px;margin-top:4px">{{ $ebook->formatted_file_size }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="padding:16px;background:#f8fafc;border-radius:12px;text-align:center">
                            <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase">Kelas</div>
                            <div style="font-weight:700;font-size:18px;margin-top:4px">{{ $ebook->kelas_tujuan ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                @if($ebook->publisher)
                <p style="font-size:14px"><strong>Penerbit:</strong> {{ $ebook->publisher }}</p>
                @endif
                @if($ebook->isbn)
                <p style="font-size:14px"><strong>ISBN:</strong> {{ $ebook->isbn }}</p>
                @endif
                @if($ebook->description)
                <div class="mt-3">
                    <h6 style="font-weight:700">Deskripsi</h6>
                    <p style="color:#64748b;line-height:1.8;font-size:14px">{{ $ebook->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($relatedEbooks->count() > 0)
<h5 class="mt-5 mb-3" style="font-weight:700"><i class="bi bi-collection me-2"></i>eBook Terkait</h5>
<div class="row g-4">
    @foreach($relatedEbooks as $rel)
    <div class="col-xl-3 col-md-6">
        <div class="card-modern" style="transition:all .3s" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
            <div style="height:140px;background:linear-gradient(135deg,#1e40af,#3b82f6);display:flex;align-items:center;justify-content:center">
                @if($rel->cover_image)
                <img src="{{ asset('storage/'.$rel->cover_image) }}" style="width:100%;height:100%;object-fit:cover" alt="">
                @else
                <i class="bi bi-book" style="font-size:40px;color:rgba(255,255,255,0.2)"></i>
                @endif
            </div>
            <div class="card-body">
                <h6 style="font-weight:700;font-size:14px">{{ Str::limit($rel->title, 30) }}</h6>
                <p style="font-size:12px;color:#94a3b8">{{ $rel->author }}</p>
                <a href="{{ route('ebooks.show', $rel) }}" class="btn btn-sm btn-outline-modern w-100">Detail</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
