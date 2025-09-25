<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeatmapTable extends Model
{
    protected $fillable = [
        'seatmap_id',
        'club_table_id',
        'pos_x',
        'pos_y',
        'seat_width',
        'seat_height',
        'seat_number',
    ];

    public function seatmap()
    {
        return $this->belongsTo(Seatmap::class);
    }

    public function clubTable()
    {
        return $this->belongsTo(ClubTable::class);
    }
}
