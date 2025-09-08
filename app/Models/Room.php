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
        'room_type',
        'token',
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

    // Paths that start from this room
    public function outgoingPaths()
    {

        return $this->hasMany(Path::class, 'from_room_id');
    }

    // Paths that lead to this room
    public function incomingPaths()
    {
        return $this->hasMany(Path::class, 'to_room_id');
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

    protected static function booted()
    {
        static::creating(function ($room) {
            if (empty($room->token)) {
                $room->token = self::generateSecureToken();
            }
        });
    }


    /**
     * Context-aware route key selection
     * - Use tokens for public/scanner routes (secure)
     * - Use IDs for admin routes (convenient)
     */
    public function getRouteKeyName()
    {
        $request = request();
        $currentRoute = $request->route();

        if ($currentRoute) {
            $routeName = $currentRoute->getName();
            $uri = $request->getRequestUri();

            // Use token for public scanner routes
            if ($routeName && (str_contains($routeName, 'scan') || str_contains($uri, 'scan-marker'))) {
                return 'token';
            }

            // Use token for client-facing routes
            if (str_contains($uri, '/rooms/') && !str_contains($uri, '/admin/')) {
                return 'token';
            }
        }

        // Use ID for admin routes (default)
        return 'id';
    }

    /**
     * Generate URL-safe tokens
     */
    public static function generateSecureToken()
    {
        do {
            $token = bin2hex(random_bytes(16)); // 32 character hex string
        } while (self::where('token', $token)->exists());

        return $token;
    }
}
