@extends('layouts.app')
@section('title', $ebook->title . ' - Perpus Sandikta')
@section('page-title', 'Detail eBook')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-modern animate-fadeInUp delay-1" style="overflow:hidden">
            <div style="height:320px;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#0f172a;padding:20px">
                @if($ebook->cover_image)
                <!-- Blurred background for depth -->
                <div style="position:absolute;top:0;left:0;right:0;bottom:0;background-image:url('{{ asset('storage/'.$ebook->cover_image) }}');background-size:cover;background-position:center;filter:blur(15px) opacity(0.3);transform:scale(1.15)"></div>
                <!-- Main sharp cover image -->
                <img src="{{ asset('storage/'.$ebook->cover_image) }}" style="max-width:100%;max-height:100%;object-fit:contain;position:relative;z-index:1;box-shadow:0 10px 25px rgba(0,0,0,0.4);border-radius:4px" alt="">
                @else
                <!-- Beautiful CSS Book Cover Fallback with Book Title -->
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;width:100%;padding:30px;text-align:center;color:white;background:linear-gradient(135deg, #1e3a8a, #3b82f6)">
                    <i class="bi bi-journal-text mb-3" style="font-size:48px;opacity:0.8"></i>
                    <span style="font-size:16px;font-weight:700;line-height:1.3;display:-webkit-box;-webkit-line-clamp:5;-webkit-box-orient:vertical;overflow:hidden;text-transform:capitalize">{{ $ebook->title }}</span>
                </div>
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
    <div class="col-xl-3 col-md-6 col-sm-6 col-12">
        <div class="card-modern" style="transition:all .3s" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
            <div style="height:220px;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#0f172a;padding:12px">
                @if($rel->cover_image)
                <!-- Blurred background for depth -->
                <div style="position:absolute;top:0;left:0;right:0;bottom:0;background-image:url('{{ asset('storage/'.$rel->cover_image) }}');background-size:cover;background-position:center;filter:blur(10px) opacity(0.25);transform:scale(1.15)"></div>
                <!-- Main sharp cover image -->
                <img src="{{ asset('storage/'.$rel->cover_image) }}" style="max-width:100%;max-height:100%;object-fit:contain;position:relative;z-index:1;box-shadow:0 6px 16px rgba(0,0,0,0.35);border-radius:3px" alt="">
                @else
                <!-- Beautiful CSS Book Cover Fallback with Book Title -->
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;width:100%;padding:15px;text-align:center;color:white;background:linear-gradient(135deg, #1e3a8a, #3b82f6)">
                    <i class="bi bi-journal-text mb-1" style="font-size:24px;opacity:0.8"></i>
                    <span style="font-size:11px;font-weight:700;line-height:1.2;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;text-transform:capitalize">{{ $rel->title }}</span>
                </div>
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
