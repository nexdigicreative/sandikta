<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedLogin extends Model
{
    protected $fillable = [
        'username',
        'ip_address',
        'user_agent',
        'browser',
        'reason',
    ];

    public static function recordFailure(string $username, string $reason = 'invalid_credentials'): self
    {
        $userAgent = request()->header('User-Agent', 'Unknown');

        return self::create([
            'username' => $username,
            'ip_address' => request()->ip(),
            'user_agent' => $userAgent,
            'browser' => self::parseBrowser($userAgent),
            'reason' => $reason,
        ]);
    }

    public static function recentFailureCount(string $ip, int $minutes = 15): int
    {
        return self::where('ip_address', $ip)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    private static function parseBrowser(string $userAgent): string
    {
        if (str_contains($userAgent, 'Edg/')) return 'Edge';
        if (str_contains($userAgent, 'Chrome/')) return 'Chrome';
        if (str_contains($userAgent, 'Firefox/')) return 'Firefox';
        if (str_contains($userAgent, 'Safari/')) return 'Safari';
        return 'Other';
    }
}
