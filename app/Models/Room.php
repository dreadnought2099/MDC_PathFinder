<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'qr_code_path',
        'marker_id',
        'image_path',
        'video_path',
        'office_hours',
    ];


    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function images() {
        
        return $this->hasMany(RoomImage::class);
    }
}
