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

    public function pathsFrom()
    {
        return $this->hasMany(Path::class, 'from_marker_id');
    }

    public function pathsTo()
    {
        return $this->hasMany(Path::class, 'to_marker_id');
    }
}
