<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PathImage extends Model
{
    protected $fillable = [
        'path_id',
        'image_order',
        'image_file',
    ];

    public function path() {

        return $this->belongsTo(Path::class);
    }
}
