@extends('layouts.app')
@section('title', 'Failed Logins - Perpus Sandikta')
@section('page-title', 'Failed Logins')

@section('content')
<div class="card-modern mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-9">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-modern" placeholder="Cari username atau IP...">
            </div>
            <div class="col-12 col-md-3"><button type="submit" class="btn btn-primary-modern w-100"><i class="bi bi-search me-1"></i>Cari</button></div>
        </form>
    </div>
</div>

{{-- Desktop Table --}}
<div class="card-modern d-none d-md-block">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern">
                <thead><tr><th>Waktu</th><th>Username</th><th>IP Address</th><th>Browser</th><th>Alasan</th></tr></thead>
                <tbody>
                    @forelse($failedLogins as $fl)
                    <tr>
                        <td style="font-size:12px;white-space:nowrap">{{ $fl->created_at->format('d/m/Y H:i:s') }}</td>
                        <td><strong>{{ $fl->username }}</strong></td>
                        <td style="font-size:13px">{{ $fl->ip_address }}</td>
                        <td style="font-size:13px;color:#94a3b8">{{ $fl->browser }}</td>
                        <td><span class="badge-modern badge-{{ $fl->reason === 'rate_limited' ? 'danger' : 'warning' }}">{{ $fl->reason }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada percobaan login gagal</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Mobile Card List --}}
<div class="d-md-none">
    @forelse($failedLogins as $fl)
    <div class="card-modern mb-2">
        <div class="card-body" style="padding:14px 16px">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <strong style="font-size:14px">{{ $fl->username }}</strong>
                <span class="badge-modern badge-{{ $fl->reason === 'rate_limited' ? 'danger' : 'warning' }}">{{ $fl->reason }}</span>
            </div>
            <div style="font-size:11px;color:#94a3b8">
                <i class="bi bi-clock me-1"></i>{{ $fl->created_at->format('d/m/Y H:i:s') }}
                <span class="mx-1">•</span>
                <i class="bi bi-geo-alt me-1"></i>{{ $fl->ip_address }}
            </div>
        </div>
    </div>
    @empty
    <div class="card-modern"><div class="card-body text-center text-muted py-4">Tidak ada percobaan login gagal</div></div>
    @endforelse
</div>

<div class="mt-3">{{ $failedLogins->withQueryString()->links() }}</div>
@endsection
