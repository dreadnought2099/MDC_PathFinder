<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Path extends Model
{
    protected $fillable = ['from_marker_id', 'to_marker_id', 'angle'];

    public function fromMarker()
    {
        return $this->belongsTo(Marker::class, 'from_marker_id');
    }

    public function toMarker()
    {
        return $this->belongsTo(Marker::class, 'to_marker_id');
    }
}
