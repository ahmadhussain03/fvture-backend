<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seatmap extends Model
{
    protected $fillable = [
        'name',
        'background_url',
        'map_width',
        'map_height',
    ];

    public function seatmapTables()
    {
        return $this->hasMany(SeatmapTable::class);
    }
}
