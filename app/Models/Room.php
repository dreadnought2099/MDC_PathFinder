<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Room extends Model
{
    use HasFactory, SoftDeletes;

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

    /**
     *  Accessor: Full office hours by day (Mon - Sun)
     */
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
                $start = Carbon::parse($record->start_time)->format('g:i A');
                $end   = Carbon::parse($record->end_time)->format('g:i A');
                $output[] = "{$day}: {$start} - {$end}";
            } else {
                $output[] = "{$day}: Closed";
            }
        }

        return implode("\n", $output);
    }

    /**
     *  Accessor: Group office hours by identical time ranges
     */
    public function getGroupedOfficeHoursAttribute()
    {
        $officeHoursLines = explode("\n", trim($this->formatted_office_hours));
        $groupedHours = [];

        foreach ($officeHoursLines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = explode(': ', $line, 2);
            if (count($parts) !== 2) continue;

            $day = trim($parts[0]);
            $timeRange = trim($parts[1]);

            $groupedHours[$timeRange][] = $day;
        }

        if (isset($groupedHours['Closed'])) {
            $closed = $groupedHours['Closed'];
            unset($groupedHours['Closed']);
            $groupedHours['Closed'] = $closed;
        }

        return $groupedHours;
    }


    /**
     *  Helper: Format days nicely (Mon - Wed, Mon, Wed, Fri, etc.)
     */
    public function formatDaysGroup(array $days): string
    {
        $daysOrder = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        if (count($days) <= 1) {
            return implode(', ', $days);
        }

        // Sort days by week order
        usort($days, function ($a, $b) use ($daysOrder) {
            return array_search($a, $daysOrder) <=> array_search($b, $daysOrder);
        });

        // Check if consecutive
        $isConsecutive = true;
        for ($i = 0; $i < count($days) - 1; $i++) {
            $currentIndex = array_search($days[$i], $daysOrder);
            $nextIndex = array_search($days[$i + 1], $daysOrder);
            if ($nextIndex !== $currentIndex + 1) {
                $isConsecutive = false;
                break;
            }
        }

        return ($isConsecutive && count($days) > 2)
            ? $days[0] . ' - ' . end($days)
            : implode(', ', $days);
    }
}
