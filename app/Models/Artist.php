<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Artist extends Model
{
    use HasFactory;

    protected $table = 'artists';

    protected $fillable = [
        'name',
        'image',
        'description',
    ];

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_artist', 'artist_id', 'event_id');
    }
}
