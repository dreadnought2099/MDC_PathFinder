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
    ];


    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function images()
    {

        return $this->hasMany(RoomImage::class);
    }

    public function officeHours() 
    {
        return $this->hasMany(OfficeHour::class);
    }

    public function getFormattedOfficeHoursAttribute()
    {
        if ($this->officeHours->isEmpty()) {
            return 'No office hours set';
        }

        $output = [];

        // Days in order
        $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        foreach ($daysOfWeek as $day) {
            $record = $this->officeHours->firstWhere('day', $day);

            if ($record && $record->start_time && $record->end_time) {
                $start = \Carbon\Carbon::parse($record->start_time)->format('g:i A'); // 8:00 AM
                $end   = \Carbon\Carbon::parse($record->end_time)->format('g:i A');   // 5:00 PM
                $output[] = "{$day}: {$start} - {$end}";
            } else {
                $output[] = "{$day}: Closed";
            }
        }

        return implode("\n", $output); // line break per day
    }
}
