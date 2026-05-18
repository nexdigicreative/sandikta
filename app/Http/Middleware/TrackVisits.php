<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackVisits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track GET requests and ignore Ajax/API/internal requests
        if ($request->isMethod('GET') && !$request->ajax() && !$request->prefetch()) {
            $path = $request->path();
            // Avoid logging assets, livewire, debugbar, or files
            if (!preg_match('/^(storage|assets|css|js|images|fonts|debugbar|favicon|api|up)/', $path)) {
                $this->logVisit($request);
            }
        }

        return $response;
    }

    private function logVisit(Request $request): void
    {
        $userAgent = $request->header('User-Agent', 'Unknown');
        $browser = $this->parseBrowser($userAgent);
        $platform = $this->parsePlatform($userAgent);
        $device = $this->parseDevice($userAgent);

        try {
            Visit::create([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => substr($userAgent, 0, 500),
                'browser' => $browser,
                'platform' => $platform,
                'device' => $device,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            ]);
        } catch (\Exception $e) {
            // Silently log or ignore to not break the application
            logger()->error('Failed to log visit: ' . $e->getMessage());
        }
    }

    private function parseBrowser(string $userAgent): string
    {
        if (str_contains($userAgent, 'Edg/')) return 'Edge';
        if (str_contains($userAgent, 'Chrome/')) return 'Chrome';
        if (str_contains($userAgent, 'Firefox/')) return 'Firefox';
        if (str_contains($userAgent, 'Safari/')) return 'Safari';
        if (str_contains($userAgent, 'Opera/') || str_contains($userAgent, 'OPR/')) return 'Opera';
        if (str_contains($userAgent, 'MSIE') || str_contains($userAgent, 'Trident/')) return 'Internet Explorer';
        return 'Other';
    }

    private function parsePlatform(string $userAgent): string
    {
        if (str_contains($userAgent, 'Windows NT')) return 'Windows';
        if (str_contains($userAgent, 'Macintosh') || str_contains($userAgent, 'Mac OS X')) return 'macOS';
        if (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) return 'iOS';
        if (str_contains($userAgent, 'Android')) return 'Android';
        if (str_contains($userAgent, 'Linux')) return 'Linux';
        return 'Other';
    }

    private function parseDevice(string $userAgent): string
    {
        if (str_contains($userAgent, 'iPad') || (str_contains($userAgent, 'Android') && !str_contains($userAgent, 'Mobile'))) {
            return 'Tablet';
        }
        if (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android') || str_contains($userAgent, 'Windows Phone')) {
            return 'Mobile';
        }
        return 'Desktop';
    }
}
