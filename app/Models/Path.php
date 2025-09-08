<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Path extends Model
{   
    use SoftDeletes;
    
    protected $fillable = [
        'from_room_id',
        'to_room_id',
    ];

    // Starting room
    public function fromRoom() {

        return $this->belongsTo(Room::class, 'from_room_id');
    }

     // Destination room
    public function toRoom() {

        return $this->belongsTo(Room::class, 'to_room_id');
    }

    // Images for this path
    public function images()
    {
        return $this->hasMany(PathImage::class);
    }
}
