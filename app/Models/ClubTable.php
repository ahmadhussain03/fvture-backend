<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubTable extends Model
{
    protected $fillable = [
        'name',
        'base_price',
        'image_url',
        'active_shape_url',
        'shape_url',
        'capacity',
    ];

    public function seatmapTables()
    {
        return $this->hasMany(SeatmapTable::class);
    }
}
