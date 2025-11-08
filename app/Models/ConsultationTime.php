<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationTime extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'day', 'start_time', 'end_time'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
