<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marker extends Model
{
    use HasFactory;

    protected $fillable = ['marker_id', 'pattern_url'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
