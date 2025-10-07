<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Staff extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'credentials',
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

    // Accessor to trim Staff Name
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' .
            ($this->middle_name ? $this->middle_name . ' ' : '') .
            $this->last_name .
            ($this->suffix ? ' ' . $this->suffix : ''));
    }

    public static function booted()
    {
        static::creating(function ($staff) {
            if (empty($staff->token)) {
                $staff->token = bin2hex(random_bytes(32)); // 64-char secure hex token
            }
        });
    }
}
