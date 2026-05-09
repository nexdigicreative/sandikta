@extends('layouts.app')
@section('title', 'Dashboard Superadmin - Perpus Sandikta')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card animate-fadeInUp delay-1">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon bg-gradient-blue"><i class="bi bi-people-fill"></i></div>
            </div>
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-label">Total Murid</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card animate-fadeInUp delay-2">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon bg-gradient-purple"><i class="bi bi-person-gear"></i></div>
            </div>
            <div class="stat-value">{{ $totalAdmins }}</div>
            <div class="stat-label">Total Admin</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card animate-fadeInUp delay-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon bg-gradient-cyan"><i class="bi bi-journal-richtext"></i></div>
            </div>
            <div class="stat-value">{{ $totalEbooks }}</div>
            <div class="stat-label">Total eBook</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card animate-fadeInUp delay-1">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon bg-gradient-emerald"><i class="bi bi-person-check"></i></div>
            </div>
            <div class="stat-value">{{ $activeUsers }}</div>
            <div class="stat-label">User Aktif</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card animate-fadeInUp delay-2">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon bg-gradient-rose"><i class="bi bi-person-x"></i></div>
            </div>
            <div class="stat-value">{{ $inactiveUsers }}</div>
            <div class="stat-label">User Nonaktif</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card animate-fadeInUp delay-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon bg-gradient-amber"><i class="bi bi-eye"></i></div>
            </div>
            <div class="stat-value">{{ $totalReads }}</div>
            <div class="stat-label">Total Dibaca</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart -->
    <div class="col-lg-8">
        <div class="card-modern animate-fadeInUp delay-2">
            <div class="card-header">
                <h6><i class="bi bi-bar-chart-line me-2 text-primary"></i>Statistik Pembacaan {{ date('Y') }}</h6>
            </div>
            <div class="card-body">
                <canvas id="readingChart" height="280"></canvas>
            </div>
        </div>
    </div>
    <!-- Top eBooks -->
    <div class="col-lg-4">
        <div class="card-modern animate-fadeInUp delay-3">
            <div class="card-header"><h6><i class="bi bi-trophy me-2 text-warning"></i>eBook Terpopuler</h6></div>
            <div class="card-body p-0">
                @forelse($topEbooks as $i => $book)
                <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div style="width:32px;height:32px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;color:#fff;background:{{ ['#3b82f6','#8b5cf6','#f59e0b','#10b981','#f43f5e'][$i] }}">{{ $i+1 }}</div>
                    <div class="flex-grow-1">
                        <div style="font-weight:600;font-size:13px">{{ Str::limit($book->title, 28) }}</div>
                        <div style="font-size:11px;color:#94a3b8">{{ $book->view_count }} kali dibaca</div>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-muted">Belum ada data</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-modern animate-fadeInUp delay-3">
            <div class="card-header">
                <h6><i class="bi bi-activity me-2 text-info"></i>Aktivitas Terbaru</h6>
                <a href="{{ route('superadmin.logs.index') }}" class="btn btn-sm btn-outline-modern">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @forelse($recentActivities->take(8) as $log)
                <div class="activity-item px-4">
                    <div class="activity-dot" style="background:{{ $log->severity === 'danger' ? '#ef4444' : ($log->severity === 'warning' ? '#f59e0b' : '#3b82f6') }}"></div>
                    <div class="flex-grow-1">
                        <div style="font-size:13px;font-weight:500">{{ $log->description }}</div>
                        <div style="font-size:11px;color:#94a3b8">{{ $log->user?->name ?? 'System' }} • {{ $log->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-muted">Belum ada aktivitas</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-modern animate-fadeInUp delay-4">
            <div class="card-header"><h6><i class="bi bi-journal-plus me-2 text-success"></i>eBook Terbaru</h6></div>
            <div class="card-body p-0">
                @forelse($recentEbooks as $book)
                <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div style="width:42px;height:56px;border-radius:8px;background:linear-gradient(135deg,#dbeafe,#bfdbfe);display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-file-earmark-pdf" style="color:#1e40af;font-size:20px"></i>
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:13px">{{ Str::limit($book->title, 30) }}</div>
                        <div style="font-size:11px;color:#94a3b8">{{ $book->category?->name }} • {{ $book->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-muted">Belum ada eBook</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
new Chart(document.getElementById('readingChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($months) !!},
        datasets: [{
            label: 'Jumlah Pembacaan',
            data: {!! json_encode($chartData) !!},
            backgroundColor: 'rgba(59,130,246,0.15)',
            borderColor: '#3b82f6',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});
</script>
@endpush
