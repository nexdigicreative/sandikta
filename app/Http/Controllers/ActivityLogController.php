<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FailedLogin;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');
        if ($request->filled('action')) $query->where('action', $request->action);
        if ($request->filled('severity')) $query->where('severity', $request->severity);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('description','like',"%{$s}%")->orWhereHas('user', fn($u) => $u->where('name','like',"%{$s}%")));
        }
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);
        $logs = $query->latest()->paginate(25);
        $actions = ActivityLog::distinct()->pluck('action');
        return view('superadmin.logs.index', compact('logs', 'actions'));
    }

    public function failedLogins(Request $request)
    {
        $query = FailedLogin::query();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('username','like',"%{$s}%")->orWhere('ip_address','like',"%{$s}%"));
        }
        $failedLogins = $query->latest()->paginate(25);
        return view('superadmin.logs.failed-logins', compact('failedLogins'));
    }

    public function security()
    {
        $suspiciousIps = FailedLogin::selectRaw('ip_address, COUNT(*) as attempts')
            ->where('created_at', '>=', now()->subDay())
            ->groupBy('ip_address')
            ->having('attempts', '>=', 3)
            ->orderByDesc('attempts')
            ->get();

        $illegalAccess = ActivityLog::where('action', 'illegal_access')
            ->with('user')
            ->latest()
            ->take(20)
            ->get();

        $recentLogins = ActivityLog::where('action', 'login')
            ->with('user')
            ->latest()
            ->take(20)
            ->get();

        return view('superadmin.logs.security', compact('suspiciousIps', 'illegalAccess', 'recentLogins'));
    }
}
