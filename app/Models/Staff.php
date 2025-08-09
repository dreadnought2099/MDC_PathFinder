<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory;
    use SoftDeletes;

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
