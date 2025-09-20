<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'event_date_time',
        'video',
        'banner_image',
        'other_information',
    ];

    protected function casts(): array
    {
        return [
            'event_date_time' => 'datetime',
        ];
    }

    public function djs(): BelongsToMany
    {
        return $this->belongsToMany(DJ::class, 'event_dj', 'event_id', 'dj_id');
    }
}
