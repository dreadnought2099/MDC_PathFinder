<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeHour extends Model
{
    protected $fillable = ['room_id', 'day', 'start_time', 'end_time'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
