@extends('layouts.app')
@section('title', 'Activity Logs - Perpus Sandikta')
@section('page-title', 'Activity Logs')

@section('content')
<div class="card-modern mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-modern" placeholder="Cari...">
            </div>
            <div class="col-md-2">
                <select name="action" class="form-control form-control-modern">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $act)
                    <option value="{{ $act }}" {{ request('action')==$act?'selected':'' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="severity" class="form-control form-control-modern">
                    <option value="">Semua Level</option>
                    <option value="info" {{ request('severity')=='info'?'selected':'' }}>Info</option>
                    <option value="warning" {{ request('severity')=='warning'?'selected':'' }}>Warning</option>
                    <option value="danger" {{ request('severity')=='danger'?'selected':'' }}>Danger</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-modern" placeholder="Dari">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-modern" placeholder="Sampai">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary-modern w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="card-modern">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern">
                <thead><tr><th>Waktu</th><th>User</th><th>Aksi</th><th>Deskripsi</th><th>IP</th><th>Browser</th><th>Level</th></tr></thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="font-size:12px;white-space:nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td style="font-weight:600;font-size:13px">{{ $log->user?->name ?? 'System' }}</td>
                        <td><span class="badge-modern badge-info">{{ $log->action }}</span></td>
                        <td style="font-size:13px">{{ Str::limit($log->description, 50) }}</td>
                        <td style="font-size:12px;color:#94a3b8">{{ $log->ip_address }}</td>
                        <td style="font-size:12px;color:#94a3b8">{{ $log->browser }}</td>
                        <td>
                            <span class="badge-modern badge-{{ $log->severity === 'danger' ? 'danger' : ($log->severity === 'warning' ? 'warning' : 'success') }}">
                                {{ $log->severity }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada log</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $logs->withQueryString()->links() }}</div>
@endsection
