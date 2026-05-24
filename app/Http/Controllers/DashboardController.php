<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Ebook;
use App\Models\ReadingHistory;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function superadmin()
    {
        $stats = Cache::remember('superadmin_dashboard_stats', 600, function () {
            $monthlyStats = ReadingHistory::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $chartData = [];
            for ($i = 1; $i <= 12; $i++) {
                $chartData[] = $monthlyStats[$i] ?? 0;
            }

            return [
                'totalUsers' => User::where('role', 'user')->count(),
                'totalAdmins' => User::where('role', 'admin')->count(),
                'totalEbooks' => Ebook::count(),
                'totalReads' => ReadingHistory::count(),
                'chartData' => $chartData,
                'totalDurationSeconds' => ReadingHistory::sum('duration_seconds'),
                'activeReadersCount' => ReadingHistory::distinct('user_id')->count('user_id'),
            ];
        });

        extract($stats);

        $topEbooks = Ebook::orderBy('view_count', 'desc')->take(5)->get();
        $topReaders = ReadingHistory::whereHas('user')
            ->with(['user'])
            ->selectRaw('user_id, count(ebook_id) as books_count, sum(read_count) as total_read_count, sum(duration_seconds) as total_duration')
            ->groupBy('user_id')
            ->orderBy('total_read_count', 'desc')
            ->take(5)
            ->get();

        $recentActivities = ActivityLog::with('user')->latest()->take(15)->get();
        $recentEbooks = Ebook::with('category')->latest()->take(5)->get();
        $recentReads = ReadingHistory::whereHas('user')->whereHas('ebook')
            ->with(['user', 'ebook'])->latest('last_read_at')->take(5)->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $totalReadingHours = floor($totalDurationSeconds / 3600);

        return view('superadmin.dashboard', compact(
            'totalUsers',
            'totalAdmins',
            'totalEbooks',
            'totalReads',
            'recentActivities',
            'recentEbooks',
            'topEbooks',
            'chartData',
            'months',
            'totalReadingHours',
            'activeReadersCount',
            'topReaders',
            'recentReads'
        ));
    }

    public function admin()
    {
        $stats = Cache::remember('admin_dashboard_stats', 600, function () {
            $monthlyStats = ReadingHistory::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $chartData = [];
            for ($i = 1; $i <= 12; $i++) {
                $chartData[] = $monthlyStats[$i] ?? 0;
            }

            return [
                'totalUsers' => User::where('role', 'user')->count(),
                'totalEbooks' => Ebook::count(),
                'activeUsers' => User::where('role', 'user')->where('is_active', true)->count(),
                'totalReads' => ReadingHistory::count(),
                'chartData' => $chartData,
                'totalDurationSeconds' => ReadingHistory::sum('duration_seconds'),
                'activeReadersCount' => ReadingHistory::distinct('user_id')->count('user_id'),
            ];
        });

        extract($stats);

        $topEbooks = Ebook::orderBy('view_count', 'desc')->take(5)->get();
        $topReaders = ReadingHistory::whereHas('user')
            ->with(['user'])
            ->selectRaw('user_id, count(ebook_id) as books_count, sum(read_count) as total_read_count, sum(duration_seconds) as total_duration')
            ->groupBy('user_id')
            ->orderBy('total_read_count', 'desc')
            ->take(5)
            ->get();

        $recentActivities = ActivityLog::with('user')->latest()->take(10)->get();
        $recentEbooks = Ebook::with('category')->latest()->take(5)->get();
        $recentReads = ReadingHistory::whereHas('user')->whereHas('ebook')
            ->with(['user', 'ebook'])->latest('last_read_at')->take(5)->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $totalReadingHours = floor($totalDurationSeconds / 3600);

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalEbooks',
            'activeUsers',
            'totalReads',
            'recentActivities',
            'recentEbooks',
            'topEbooks',
            'chartData',
            'months',
            'totalReadingHours',
            'activeReadersCount',
            'topReaders',
            'recentReads'
        ));
    }

    public function user()
    {
        $user = Auth::user();

        $totalEbooks = Cache::remember('user_total_ebooks', 3600, function () {
            return Ebook::where('is_active', true)->count();
        });

        $totalRead = Cache::remember("user_{$user->id}_total_read", 300, function () use ($user) {
            return ReadingHistory::where('user_id', $user->id)->count();
        });

        $readingHistories = ReadingHistory::where('user_id', $user->id)
            ->with('ebook.category')
            ->latest('last_read_at')
            ->take(5)
            ->get();

        $recentEbooks = Ebook::where('is_active', true)
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        return view('user.dashboard', compact(
            'totalEbooks',
            'readingHistories',
            'recentEbooks',
            'totalRead'
        ));
    }
}
