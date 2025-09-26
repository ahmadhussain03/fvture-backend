<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;

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

    protected $appends = ['shape_url_full'];

    public function seatmapTables()
    {
        return $this->hasMany(SeatmapTable::class);
    }

    public function getShapeUrlFullAttribute()
    {
        if ($this->shape_url) {
            return Storage::disk('s3')->url($this->shape_url);
        }
        return null;
    }
}
