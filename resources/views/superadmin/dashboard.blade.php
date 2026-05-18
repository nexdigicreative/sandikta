@extends('layouts.app')
@section('title', 'Dashboard Superadmin - Perpus Sandikta')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Row -->
<div class="row g-3 g-md-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card animate-fadeInUp delay-1">
            <div class="d-flex align-items-center justify-content-between mb-2 mb-md-3">
                <div class="stat-icon bg-gradient-blue"><i class="bi bi-people-fill"></i></div>
            </div>
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-label">Total Murid</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card animate-fadeInUp delay-2">
            <div class="d-flex align-items-center justify-content-between mb-2 mb-md-3">
                <div class="stat-icon bg-gradient-purple"><i class="bi bi-person-gear"></i></div>
            </div>
            <div class="stat-value">{{ $totalAdmins }}</div>
            <div class="stat-label">Total Admin</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card animate-fadeInUp delay-3">
            <div class="d-flex align-items-center justify-content-between mb-2 mb-md-3">
                <div class="stat-icon bg-gradient-cyan"><i class="bi bi-journal-richtext"></i></div>
            </div>
            <div class="stat-value">{{ $totalEbooks }}</div>
            <div class="stat-label">Total eBook</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card animate-fadeInUp delay-4">
            <div class="d-flex align-items-center justify-content-between mb-2 mb-md-3">
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
                <canvas id="readingChart" style="max-height:280px;width:100%"></canvas>
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

<!-- Live Reading Stats & Top Readers Header -->
<div class="d-flex justify-content-between align-items-center mb-3 mt-4 animate-fadeInUp">
    <h5 class="mb-0" style="font-weight: 700; color: var(--text-primary);"><i class="bi bi-book-half me-2 text-primary"></i>Aktivitas & Kinerja Membaca</h5>
    <span class="badge-modern badge-success"><span class="spinner-grow spinner-grow-sm me-1" role="status" style="width:10px;height:10px;animation-duration:1.5s"></span>Live</span>
</div>

<!-- Live Reading Stats & Top Readers -->
<div class="row g-4 mb-4">
    <!-- Live Reading Stats -->
    <div class="col-lg-8">
        <div class="card-modern h-100 animate-fadeInUp delay-3">
            <div class="card-header"><h6><i class="bi bi-book-half me-2 text-primary"></i>Aktivitas Membaca Murid</h6></div>
            <div class="card-body">
                <!-- Reading Stats Mini Summary -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="p-3" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-radius:14px;border:1px solid #bbf7d0;display:flex;align-items:center;gap:12px">
                            <div style="width:42px;height:42px;border-radius:10px;background:#10b981;color:#fff;display:flex;align-items:center;justify-content:center;font-size:20px"><i class="bi bi-clock-history"></i></div>
                            <div>
                                <div style="font-size:11px;color:#166534;font-weight:600;text-transform:uppercase">Total Jam Baca</div>
                                <div style="font-weight:800;font-size:20px;color:#14532d;margin-top:2px">{{ $totalReadingHours }} Jam</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:14px;border:1px solid #bfdbfe;display:flex;align-items:center;gap:12px">
                            <div style="width:42px;height:42px;border-radius:10px;background:#3b82f6;color:#fff;display:flex;align-items:center;justify-content:center;font-size:20px"><i class="bi bi-people"></i></div>
                            <div>
                                <div style="font-size:11px;color:#1e40af;font-weight:600;text-transform:uppercase">Siswa Aktif Membaca</div>
                                <div style="font-weight:800;font-size:20px;color:#1e3a8a;margin-top:2px">{{ $activeReadersCount }} Siswa</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 290px; overflow-y: auto;">
                    <table class="table-modern align-middle mb-0">
                        <thead>
                            <tr style="position: sticky; top: 0; background: #fff; z-index: 1;">
                                <th>Murid</th>
                                <th>eBook</th>
                                <th style="text-align: center;">Jumlah Baca</th>
                                <th style="text-align: center;">Halaman Terakhir</th>
                                <th>Terakhir Akses</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentReads as $read)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:32px;height:32px;border-radius:50%;background:#e0f2fe;color:#0369a1;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:12px">
                                            {{ strtoupper(substr($read->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <strong style="font-size:13px">{{ $read->user->name }}</strong><br>
                                            <small class="text-muted" style="font-size:11px">{{ $read->user->kelas ?? 'Kelas -' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong style="font-size:13px">{{ Str::limit($read->ebook->title, 32) }}</strong><br>
                                    <small class="text-muted" style="font-size:11px">{{ $read->ebook->category?->name ?? 'Tanpa Kategori' }}</small>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge-modern badge-info" style="font-size:11px;padding:4px 8px">{{ $read->read_count }}x</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge-modern badge-warning" style="font-size:11px;padding:4px 8px">Hal. {{ $read->last_page }}</span>
                                </td>
                                <td>
                                    <div style="font-size:12px;font-weight:500">{{ $read->last_read_at?->diffForHumans() ?? $read->updated_at->diffForHumans() }}</div>
                                    <small class="text-muted" style="font-size:10px">{{ $read->last_read_at?->format('d/m/Y H:i') ?? $read->updated_at->format('d/m/Y H:i') }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada aktivitas membaca</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Top Readers -->
    <div class="col-lg-4">
        <div class="card-modern h-100 animate-fadeInUp delay-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="bi bi-trophy me-2 text-warning"></i>Pembaca Teraktif</h6>
                <span class="badge-modern badge-info">Top 5</span>
            </div>
            <div class="card-body p-0">
                @forelse($topReaders as $i => $reader)
                <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div style="width:32px;height:32px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;color:#fff;background:{{ ['#10b981','#06b6d4','#3b82f6','#8b5cf6','#ec4899'][$i] }}">{{ $i+1 }}</div>
                    <div class="flex-grow-1">
                        <div style="font-weight:600;font-size:13px">{{ $reader->user->name }}</div>
                        <div style="font-size:11px;color:#94a3b8">{{ $reader->user->kelas ?? 'Kelas -' }}</div>
                        <div style="font-size:11px;color:#64748b;margin-top:2px"><i class="bi bi-journal-check me-1 text-primary"></i><strong>{{ $reader->books_count }}</strong> eBook • <strong>{{ $reader->total_read_count }}x</strong> dibaca</div>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-muted">Belum ada data pembaca</div>
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
<script class="dashboard-script">
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
