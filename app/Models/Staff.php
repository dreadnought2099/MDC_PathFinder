<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'bio',
        'email',
        'phone_num',
        'photo_path',
    ];


    public function room()
    {
        return $this->belongsTo(Room::class);
    }   
}
