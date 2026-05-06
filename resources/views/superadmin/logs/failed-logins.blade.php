@extends('layouts.app')
@section('title', 'Failed Logins - Perpus Sandikta')
@section('page-title', 'Failed Logins')

@section('content')
<div class="card-modern mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-9">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-modern" placeholder="Cari username atau IP...">
            </div>
            <div class="col-md-3"><button type="submit" class="btn btn-primary-modern w-100"><i class="bi bi-search me-1"></i>Cari</button></div>
        </form>
    </div>
</div>

<div class="card-modern">
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
                        <td style="font-size:13px;color:#94a3b8;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $fl->browser }}">{{ $fl->browser }}</td>
                        <td><span class="badge-modern badge-{{ $fl->reason === 'rate_limited' ? 'danger' : 'warning' }}">{{ $fl->reason }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada percobaan login gagal</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-top-0 px-4 py-3">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
            <div class="text-secondary" style="font-size: 13px;">
                Menampilkan <strong>{{ $failedLogins->firstItem() ?? 0 }}</strong> sampai <strong>{{ $failedLogins->lastItem() ?? 0 }}</strong> dari <strong>{{ $failedLogins->total() }}</strong> log
            </div>
            <div class="pagination-modern">
                {{ $failedLogins->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
