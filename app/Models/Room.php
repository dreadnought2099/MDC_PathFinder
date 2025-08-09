<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'qr_code_path',
        'marker_id',
        'image_path',
        'video_path',
        'office_days',
        'office_hours_start',
        'office_hours_end',
    ];


    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function images()
    {

        return $this->hasMany(RoomImage::class);
    }

    public function getOfficeHoursFormattedAttribute()
    {
        if ($this->office_days && $this->office_hours_start && $this->office_hours_end) {
            $days = str_replace(',', ', ', $this->office_days);
            $start = date('H:i', strtotime($this->office_hours_start));
            $end = date('H:i', strtotime($this->office_hours_end));
            return "{$days} {$start} - {$end}";
        }
        return null;
    }
}
