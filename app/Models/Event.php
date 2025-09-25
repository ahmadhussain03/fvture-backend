<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'from_date',
        'to_date',
        'video',
        'banner_image',
        'other_information',
    ];

    protected function casts(): array
    {
        return [
            'from_date' => 'datetime',
            'to_date' => 'datetime',
        ];
    }

    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'event_artist', 'event_id', 'artist_id');
    }

    /**
     * Get the gallery items for the event
     */
    public function gallery(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }
}
