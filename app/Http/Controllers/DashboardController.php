<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Ebook;
use App\Models\ReadingHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function superadmin()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalEbooks = Ebook::count();
        $activeUsers = User::where('role', 'user')->where('is_active', true)->count();
        $inactiveUsers = User::where('role', 'user')->where('is_active', false)->count();
        $totalReads = ReadingHistory::count();
        
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(15)
            ->get();

        $recentEbooks = Ebook::with('category')
            ->latest()
            ->take(5)
            ->get();

        $topEbooks = Ebook::orderBy('view_count', 'desc')
            ->take(5)
            ->get();

        // Monthly reading stats for chart
        $monthlyStats = ReadingHistory::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $chartData = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlyStats[$i] ?? 0;
        }

        return view('superadmin.dashboard', compact(
            'totalUsers', 'totalAdmins', 'totalEbooks', 'activeUsers', 
            'inactiveUsers', 'totalReads', 'recentActivities', 'recentEbooks',
            'topEbooks', 'chartData', 'months'
        ));
    }

    public function admin()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalEbooks = Ebook::count();
        $activeUsers = User::where('role', 'user')->where('is_active', true)->count();
        $totalReads = ReadingHistory::count();
        
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        $recentEbooks = Ebook::with('category')
            ->latest()
            ->take(5)
            ->get();

        $topEbooks = Ebook::orderBy('view_count', 'desc')
            ->take(5)
            ->get();

        $monthlyStats = ReadingHistory::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $chartData = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlyStats[$i] ?? 0;
        }

        return view('admin.dashboard', compact(
            'totalUsers', 'totalEbooks', 'activeUsers', 'totalReads',
            'recentActivities', 'recentEbooks', 'topEbooks', 'chartData', 'months'
        ));
    }

    public function user()
    {
        $user = Auth::user();
        $totalEbooks = Ebook::where('is_active', true)->count();
        
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

        $totalRead = ReadingHistory::where('user_id', $user->id)->count();

        return view('user.dashboard', compact(
            'totalEbooks', 'readingHistories', 'recentEbooks', 'totalRead'
        ));
    }
}
