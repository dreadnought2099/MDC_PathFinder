<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
     use HasFactory;

    protected $fillable = ['name', 'marker_id'];

    public function marker()
    {
        return $this->belongsTo(Marker::class);
    }
}
