<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'type',
        'file_size',
        'mime_type',
        'event_id',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the event that owns the gallery item
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the full URL for the file
     */
    public function getFileUrlAttribute(): string
    {
        return config('filesystems.disks.s3.url') . '/' . $this->file_path;
    }

    /**
     * Check if the gallery item is an image
     */
    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    /**
     * Check if the gallery item is a video
     */
    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
