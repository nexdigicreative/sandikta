<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoLogoutIdle
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $maxIdleTime = 30 * 60; // 30 minutes
            $lastActivity = session('last_activity_time');

            if ($lastActivity && (time() - $lastActivity > $maxIdleTime)) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('warning', 'Sesi Anda telah berakhir karena tidak aktif.');
            }

            session(['last_activity_time' => time()]);
        }

        return $next($request);
    }
}
