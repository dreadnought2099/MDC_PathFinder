<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'name',
        'position',
        'bio',
        'email',
        'contact_num',
        'photo_path',
    ];


    public function room()
    {
        return $this->belongsTo(Room::class);
    }   
}
