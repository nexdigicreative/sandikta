@extends('layouts.app')
@section('title', 'Security Monitoring - Perpus Sandikta')
@section('page-title', 'Security Monitoring')

@section('content')
<div class="row g-4 mb-4">
    <!-- Suspicious IPs -->
    <div class="col-lg-6">
        <div class="card-modern">
            <div class="card-header"><h6><i class="bi bi-exclamation-triangle me-2 text-danger"></i>IP Mencurigakan (24 Jam)</h6></div>
            <div class="card-body p-0">
                @forelse($suspiciousIps as $ip)
                <div class="d-flex align-items-center justify-content-between px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div>
                        <div style="font-weight:600;font-size:14px"><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $ip->ip_address }}</div>
                    </div>
                    <span class="badge-modern badge-danger">{{ $ip->attempts }} percobaan gagal</span>
                </div>
                @empty
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-shield-check" style="font-size:32px;color:#10b981"></i>
                    <p class="mt-2 mb-0">Tidak ada IP mencurigakan</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Illegal Access Attempts -->
    <div class="col-lg-6">
        <div class="card-modern">
            <div class="card-header"><h6><i class="bi bi-shield-x me-2 text-warning"></i>Percobaan Akses Ilegal</h6></div>
            <div class="card-body p-0">
                @forelse($illegalAccess as $log)
                <div class="activity-item px-4">
                    <div class="activity-dot" style="background:#ef4444"></div>
                    <div class="flex-grow-1">
                        <div style="font-size:13px;font-weight:500">{{ $log->description }}</div>
                        <div style="font-size:11px;color:#94a3b8">
                            {{ $log->user?->name ?? 'Unknown' }} • {{ $log->ip_address }} • {{ $log->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-shield-check" style="font-size:32px;color:#10b981"></i>
                    <p class="mt-2 mb-0">Tidak ada percobaan akses ilegal</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Recent Logins -->
<div class="card-modern">
    <div class="card-header"><h6><i class="bi bi-box-arrow-in-right me-2 text-primary"></i>Login Terbaru</h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern">
                <thead><tr><th>Waktu</th><th>User</th><th>IP Address</th><th>Browser</th></tr></thead>
                <tbody>
                    @forelse($recentLogins as $login)
                    <tr>
                        <td style="font-size:12px;white-space:nowrap">{{ $login->created_at->format('d/m/Y H:i') }}</td>
                        <td style="font-weight:600">{{ $login->user?->name ?? '-' }}</td>
                        <td style="font-size:13px">{{ $login->ip_address }}</td>
                        <td style="font-size:13px;color:#94a3b8">{{ $login->browser }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data login</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
