<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'target_type',
        'target_id',
        'ip_address',
        'user_agent',
        'browser',
        'extra_data',
        'severity',
    ];

    protected $casts = [
        'extra_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(
        string $action,
        string $description,
        ?string $targetType = null,
        ?int $targetId = null,
        string $severity = 'info',
        ?array $extraData = null
    ): self {
        $userAgent = Request::header('User-Agent', 'Unknown');

        return self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'ip_address' => Request::ip(),
            'user_agent' => $userAgent,
            'browser' => self::parseBrowser($userAgent),
            'extra_data' => $extraData,
            'severity' => $severity,
        ]);
    }

    private static function parseBrowser(string $userAgent): string
    {
        if (str_contains($userAgent, 'Edg/')) return 'Edge';
        if (str_contains($userAgent, 'Chrome/')) return 'Chrome';
        if (str_contains($userAgent, 'Firefox/')) return 'Firefox';
        if (str_contains($userAgent, 'Safari/')) return 'Safari';
        if (str_contains($userAgent, 'Opera/') || str_contains($userAgent, 'OPR/')) return 'Opera';
        return 'Other';
    }

    public function getSeverityBadgeAttribute(): string
    {
        return match ($this->severity) {
            'warning' => 'warning',
            'danger' => 'danger',
            default => 'info',
        };
    }
}
