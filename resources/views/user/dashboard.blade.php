@extends('layouts.app')
@section('title', 'Dashboard - Perpus Sandikta')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card animate-fadeInUp delay-1">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon bg-gradient-blue"><i class="bi bi-journal-richtext"></i></div>
            </div>
            <div class="stat-value">{{ $totalEbooks }}</div>
            <div class="stat-label">Total eBook Tersedia</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card animate-fadeInUp delay-2">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon bg-gradient-emerald"><i class="bi bi-book"></i></div>
            </div>
            <div class="stat-value">{{ $totalRead }}</div>
            <div class="stat-label">eBook Telah Dibaca</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card animate-fadeInUp delay-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon bg-gradient-purple"><i class="bi bi-person-circle"></i></div>
            </div>
            <div class="stat-value">{{ Auth::user()->kelas ?? '-' }}</div>
            <div class="stat-label">Kelas</div>
        </div>
    </div>
</div>

<!-- Reading History -->
@if($readingHistories->count() > 0)
<div class="card-modern mb-4 animate-fadeInUp delay-2">
    <div class="card-header">
        <h6><i class="bi bi-clock-history me-2 text-primary"></i>Terakhir Dibaca</h6>
    </div>
    <div class="card-body p-0">
        @foreach($readingHistories as $history)
        <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
            <div style="width:48px;height:64px;border-radius:10px;background:linear-gradient(135deg,#dbeafe,#bfdbfe);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="bi bi-file-earmark-pdf" style="color:#1e40af;font-size:22px"></i>
            </div>
            <div class="flex-grow-1">
                <div style="font-weight:600;font-size:14px">{{ $history->ebook->title }}</div>
                <div style="font-size:12px;color:#94a3b8">{{ $history->ebook->category?->name }} • Dibaca {{ $history->read_count }}x</div>
            </div>
            <div class="text-end">
                <div style="font-size:11px;color:#94a3b8">{{ $history->last_read_at?->diffForHumans() }}</div>
                <a href="{{ route('ebooks.read', $history->ebook) }}" class="btn btn-sm btn-primary-modern mt-1" style="padding:4px 14px;font-size:12px">Baca</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Recent eBooks -->
<div class="card-modern animate-fadeInUp delay-3">
    <div class="card-header">
        <h6><i class="bi bi-stars me-2 text-warning"></i>eBook Terbaru</h6>
        <a href="{{ route('ebooks.index') }}" class="btn btn-sm btn-outline-modern">Lihat Semua</a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @forelse($recentEbooks as $ebook)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div style="border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;transition:all .3s;cursor:pointer" 
                     onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 25px rgba(0,0,0,0.08)'" 
                     onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div style="height:140px;background:linear-gradient(135deg,#1e40af,#3b82f6);display:flex;align-items:center;justify-content:center">
                        @if($ebook->cover_image)
                        <img src="{{ asset('storage/'.$ebook->cover_image) }}" style="width:100%;height:100%;object-fit:cover" alt="">
                        @else
                        <i class="bi bi-book" style="font-size:48px;color:rgba(255,255,255,0.3)"></i>
                        @endif
                    </div>
                    <div style="padding:16px">
                        <div style="font-weight:700;font-size:14px;margin-bottom:4px">{{ Str::limit($ebook->title, 30) }}</div>
                        <div style="font-size:12px;color:#64748b;margin-bottom:8px">{{ $ebook->author }}</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="badge-modern badge-info">{{ $ebook->category?->name }}</span>
                            <a href="{{ route('ebooks.show', $ebook) }}" style="font-size:12px;font-weight:600;color:#3b82f6;text-decoration:none">Detail →</a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-journal-x" style="font-size:48px;opacity:0.3"></i>
                <p class="mt-2">Belum ada eBook tersedia</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
