<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Ebook extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'author',
        'publisher',
        'year',
        'isbn',
        'category_id',
        'cover_image',
        'file_path',
        'file_hash',
        'file_size',
        'total_pages',
        'kelas_tujuan',
        'is_active',
        'uploaded_by',
        'view_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'year' => 'integer',
        'file_size' => 'integer',
        'total_pages' => 'integer',
        'view_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($ebook) {
            if (empty($ebook->slug)) {
                $ebook->slug = Str::slug($ebook->title) . '-' . Str::random(5);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function readingHistories(): HasMany
    {
        return $this->hasMany(ReadingHistory::class);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}
