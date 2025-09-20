<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DJ extends Model
{
    use HasFactory;

    protected $table = 'djs';

    protected $fillable = [
        'name',
        'image',
        'description',
    ];

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_dj', 'dj_id', 'event_id');
    }
}
