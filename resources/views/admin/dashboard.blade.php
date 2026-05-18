@extends('layouts.app')
@section('title', 'Dashboard Admin - Perpus Sandikta')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card animate-fadeInUp delay-1">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon bg-gradient-blue"><i class="bi bi-people-fill"></i></div>
            </div>
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-label">Total Murid</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card animate-fadeInUp delay-2">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon bg-gradient-cyan"><i class="bi bi-journal-richtext"></i></div>
            </div>
            <div class="stat-value">{{ $totalEbooks }}</div>
            <div class="stat-label">Total eBook</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card animate-fadeInUp delay-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon bg-gradient-emerald"><i class="bi bi-person-check"></i></div>
            </div>
            <div class="stat-value">{{ $activeUsers }}</div>
            <div class="stat-label">User Aktif</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card animate-fadeInUp delay-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon bg-gradient-amber"><i class="bi bi-eye"></i></div>
            </div>
            <div class="stat-value">{{ $totalReads }}</div>
            <div class="stat-label">Total Dibaca</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card-modern animate-fadeInUp delay-2">
            <div class="card-header"><h6><i class="bi bi-bar-chart-line me-2 text-primary"></i>Statistik Pembacaan {{ date('Y') }}</h6></div>
            <div class="card-body"><canvas id="readingChart" height="280"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-modern animate-fadeInUp delay-3">
            <div class="card-header"><h6><i class="bi bi-trophy me-2 text-warning"></i>eBook Terpopuler</h6></div>
            <div class="card-body p-0">
                @forelse($topEbooks as $i => $book)
                <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div style="width:32px;height:32px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;color:#fff;background:{{ ['#3b82f6','#8b5cf6','#f59e0b','#10b981','#f43f5e'][$i] }}">{{ $i+1 }}</div>
                    <div class="flex-grow-1">
                        <div style="font-weight:600;font-size:13px">{{ Str::limit($book->title, 28) }}</div>
                        <div style="font-size:11px;color:#94a3b8">{{ $book->view_count }} dibaca</div>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-muted">Belum ada data</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Visitor Section Header -->
<div class="d-flex justify-content-between align-items-center mb-3 mt-4 animate-fadeInUp">
    <h5 class="mb-0" style="font-weight: 700; color: var(--text-primary);"><i class="bi bi-people-fill me-2 text-primary"></i>Statistik & Analitik Pengunjung</h5>
    <span class="badge-modern badge-info"><i class="bi bi-calendar-event me-1"></i>Realtime Traffic</span>
</div>

<!-- Visitor Stats Row -->
<div class="row g-3 g-md-4 mb-4">
    <!-- Stat 1 -->
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card animate-fadeInUp delay-1">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon bg-gradient-blue"><i class="bi bi-eye"></i></div>
            </div>
            <div class="stat-value">{{ number_format($totalPageviews) }}</div>
            <div class="stat-label">Total Kunjungan Halaman</div>
        </div>
    </div>
    <!-- Stat 2 -->
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card animate-fadeInUp delay-2">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon bg-gradient-purple"><i class="bi bi-people"></i></div>
            </div>
            <div class="stat-value">{{ number_format($totalUniqueVisitors) }}</div>
            <div class="stat-label">Total Pengunjung Unik</div>
        </div>
    </div>
    <!-- Stat 3 -->
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card animate-fadeInUp delay-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon bg-gradient-cyan"><i class="bi bi-graph-up-arrow"></i></div>
            </div>
            <div class="stat-value">{{ number_format($todayPageviews) }}</div>
            <div class="stat-label">Kunjungan Hari Ini</div>
        </div>
    </div>
    <!-- Stat 4 -->
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card animate-fadeInUp delay-1">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon bg-gradient-emerald"><i class="bi bi-person-heart"></i></div>
            </div>
            <div class="stat-value">{{ number_format($todayUniqueVisitors) }}</div>
            <div class="stat-label">Pengunjung Unik Hari Ini</div>
        </div>
    </div>
</div>

<!-- Visitor Charts and Distribution -->
<div class="row g-4 mb-4">
    <!-- Visitor Line Chart -->
    <div class="col-lg-8">
        <div class="card-modern animate-fadeInUp delay-2">
            <div class="card-header">
                <h6><i class="bi bi-activity me-2 text-primary"></i>Tren Kunjungan Pengunjung (10 Hari Terakhir)</h6>
            </div>
            <div class="card-body">
                <canvas id="visitorChart" style="max-height:280px;width:100%"></canvas>
            </div>
        </div>
    </div>
    <!-- Distributions (Browser / OS / Device) -->
    <div class="col-lg-4">
        <div class="card-modern animate-fadeInUp delay-3">
            <div class="card-header">
                <h6><i class="bi bi-laptop me-2 text-warning"></i>Distribusi Sistem & Perangkat</h6>
            </div>
            <div class="card-body">
                <!-- Nav tabs for distribution -->
                <ul class="nav nav-tabs nav-tabs-modern mb-3" id="distTabs" role="tablist" style="border-bottom: 2px solid var(--border-color)">
                    <li class="nav-item" role="presentation" style="flex: 1; text-align: center;">
                        <button class="nav-link active py-2" id="browser-tab" data-bs-toggle="tab" data-bs-target="#browser-pane" type="button" role="tab" style="width: 100%; font-weight: 600; font-size: 12px; border: none; background: transparent; transition: var(--transition)">Browser</button>
                    </li>
                    <li class="nav-item" role="presentation" style="flex: 1; text-align: center;">
                        <button class="nav-link py-2" id="os-tab" data-bs-toggle="tab" data-bs-target="#os-pane" type="button" role="tab" style="width: 100%; font-weight: 600; font-size: 12px; border: none; background: transparent; transition: var(--transition)">OS</button>
                    </li>
                    <li class="nav-item" role="presentation" style="flex: 1; text-align: center;">
                        <button class="nav-link py-2" id="device-tab" data-bs-toggle="tab" data-bs-target="#device-pane" type="button" role="tab" style="width: 100%; font-weight: 600; font-size: 12px; border: none; background: transparent; transition: var(--transition)">Perangkat</button>
                    </li>
                </ul>
                <div class="tab-content" id="distTabsContent">
                    <!-- Browser Tab Pane -->
                    <div class="tab-pane fade show active" id="browser-pane" role="tabpanel">
                        @php $totalBrowsers = $browserStats->sum('total') ?: 1; @endphp
                        @foreach($browserStats->take(5) as $stat)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1" style="font-size: 12px; font-weight: 600">
                                <span>{{ $stat->browser ?: 'Other' }}</span>
                                <span class="text-muted">{{ round(($stat->total / $totalBrowsers) * 100) }}% ({{ $stat->total }})</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 3px; background-color: #f1f5f9">
                                <div class="progress-bar" role="progressbar" style="width: {{ ($stat->total / $totalBrowsers) * 100 }}%; border-radius: 3px; background: var(--primary-gradient)"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <!-- OS Tab Pane -->
                    <div class="tab-pane fade" id="os-pane" role="tabpanel">
                        @php $totalPlatforms = $platformStats->sum('total') ?: 1; @endphp
                        @foreach($platformStats->take(5) as $stat)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1" style="font-size: 12px; font-weight: 600">
                                <span>{{ $stat->platform ?: 'Other' }}</span>
                                <span class="text-muted">{{ round(($stat->total / $totalPlatforms) * 100) }}% ({{ $stat->total }})</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 3px; background-color: #f1f5f9">
                                <div class="progress-bar" role="progressbar" style="width: {{ ($stat->total / $totalPlatforms) * 100 }}%; border-radius: 3px; background: linear-gradient(135deg, #06b6d4, #0891b2)"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <!-- Device Tab Pane -->
                    <div class="tab-pane fade" id="device-pane" role="tabpanel">
                        @php $totalDevices = $deviceStats->sum('total') ?: 1; @endphp
                        @foreach($deviceStats->take(5) as $stat)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1" style="font-size: 12px; font-weight: 600">
                                <span>{{ $stat->device ?: 'Other' }}</span>
                                <span class="text-muted">{{ round(($stat->total / $totalDevices) * 100) }}% ({{ $stat->total }})</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 3px; background-color: #f1f5f9">
                                <div class="progress-bar" role="progressbar" style="width: {{ ($stat->total / $totalDevices) * 100 }}%; border-radius: 3px; background: linear-gradient(135deg, #8b5cf6, #6d28d9)"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Visited Pages Row -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card-modern animate-fadeInUp delay-3">
            <div class="card-header">
                <h6><i class="bi bi-file-earmark-bar-graph me-2 text-success"></i>Halaman Paling Sering Dikunjungi</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table-modern align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 70%">URL Halaman</th>
                                <th style="text-align: center; width: 25%">Jumlah Kunjungan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topPages as $i => $page)
                            <tr>
                                <td>
                                    <div style="width:24px;height:24px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:11px;color:#fff;background:{{ ['#3b82f6','#8b5cf6','#f59e0b','#10b981','#f43f5e'][$i] ?? '#64748b' }}">{{ $i+1 }}</div>
                                </td>
                                <td>
                                    <code class="text-primary" style="font-size: 13px; font-weight: 600">{{ $page->url }}</code>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge-modern badge-info" style="font-size:12px;padding:5px 12px;font-weight:700">{{ number_format($page->total) }}x Kunjungan</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Belum ada aktivitas kunjungan halaman</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Live Reading Stats & Top Readers Header -->
<div class="d-flex justify-content-between align-items-center mb-3 mt-4 animate-fadeInUp">
    <h5 class="mb-0" style="font-weight: 700; color: var(--text-primary);"><i class="bi bi-book-half me-2 text-primary"></i>Aktivitas & Kinerja Membaca</h5>
    <span class="badge-modern badge-success"><span class="spinner-grow spinner-grow-sm me-1" role="status" style="width:10px;height:10px;animation-duration:1.5s"></span>Active Readers</span>
</div>

<!-- Live Reading Stats & Top Readers -->
<div class="row g-4 mb-4">
    <!-- Live Reading Stats -->
    <div class="col-lg-8">
        <div class="card-modern h-100 animate-fadeInUp delay-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="bi bi-book-half me-2 text-primary"></i>Statistik Aktivitas Membaca Murid</h6>
                <span class="badge-modern badge-success"><span class="spinner-grow spinner-grow-sm me-1" role="status" style="width:10px;height:10px;animation-duration:1.5s"></span>Live Updates</span>
            </div>
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

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card-modern animate-fadeInUp delay-3">
            <div class="card-header"><h6><i class="bi bi-activity me-2 text-info"></i>Aktivitas Terbaru</h6></div>
            <div class="card-body p-0">
                @forelse($recentActivities as $log)
                <div class="activity-item px-4">
                    <div class="activity-dot" style="background:{{ $log->severity === 'danger' ? '#ef4444' : '#3b82f6' }}"></div>
                    <div>
                        <div style="font-size:13px;font-weight:500">{{ Str::limit($log->description, 50) }}</div>
                        <div style="font-size:11px;color:#94a3b8">{{ $log->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-muted">Belum ada aktivitas</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-6">
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
// Reading Stats Chart
new Chart(document.getElementById('readingChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($months) !!},
        datasets: [{
            label: 'Pembacaan', data: {!! json_encode($chartData) !!},
            backgroundColor: 'rgba(59,130,246,0.15)', borderColor: '#3b82f6',
            borderWidth: 2, borderRadius: 8, borderSkipped: false,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
    }
});

// Visitor Stats Chart
const visitorCtx = document.getElementById('visitorChart').getContext('2d');
const visitorGradientPageviews = visitorCtx.createLinearGradient(0, 0, 0, 200);
visitorGradientPageviews.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
visitorGradientPageviews.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

const visitorGradientUnique = visitorCtx.createLinearGradient(0, 0, 0, 200);
visitorGradientUnique.addColorStop(0, 'rgba(139, 92, 246, 0.3)');
visitorGradientUnique.addColorStop(1, 'rgba(139, 92, 246, 0.0)');

new Chart(document.getElementById('visitorChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($visitorChartLabels) !!},
        datasets: [
            {
                label: 'Kunjungan Halaman',
                data: {!! json_encode($visitorChartPageviews) !!},
                borderColor: '#3b82f6',
                backgroundColor: visitorGradientPageviews,
                fill: true,
                tension: 0.35,
                borderWidth: 3,
                pointBackgroundColor: '#3b82f6',
                pointHoverRadius: 6
            },
            {
                label: 'Pengunjung Unik',
                data: {!! json_encode($visitorChartUnique) !!},
                borderColor: '#8b5cf6',
                backgroundColor: visitorGradientUnique,
                fill: true,
                tension: 0.35,
                borderWidth: 3,
                pointBackgroundColor: '#8b5cf6',
                pointHoverRadius: 6
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    font: {
                        family: "'Inter', sans-serif",
                        size: 11,
                        weight: '600'
                    },
                    boxWidth: 10,
                    boxHeight: 10,
                    borderRadius: 3
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#f1f5f9' },
                ticks: {
                    font: { family: "'Inter', sans-serif", size: 10, weight: '500' }
                }
            },
            x: {
                grid: { display: false },
                ticks: {
                    font: { family: "'Inter', sans-serif", size: 10, weight: '500' }
                }
            }
        }
    }
});
</script>
@endpush
