<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $with = [
        'officeHours',
        'consultationTimes',
    ];

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

    public function consultationTimes()
    {
        return $this->hasMany(ConsultationTime::class);
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

    public function officeManager()
    {
        return $this->hasOne(User::class)->whereHas('roles', fn($q) => $q->where('name', 'Office Manager'));
    }

    // Add valid days helper
    public static function validDays(): array
    {
        return ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
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



    /**
     *  Accessor: Full consultation time by day (Mon - Sun)
     */
    public function getFormattedConsultationTimesAttribute()
    {
        if ($this->consultationTimes->isEmpty()) {
            return 'No consultation time set';
        }

        $output = [];

        // Days in order
        $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        foreach ($daysOfWeek as $day) {
            $record = $this->consultationTimes->firstWhere('day', $day);

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
     *  Accessor: Group consultation time by identical time ranges
     */
    public function getGroupedConsultationTimesAttribute()
    {
        $consultationLines = explode("\n", trim($this->formatted_consultation_times));
        $groupedHours = [];

        foreach ($consultationLines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = explode(': ', $line, 2);
            if (count($parts) !== 2) continue;

            $day = trim($parts[0]);
            $timeRange = trim($parts[1]);

            $groupedHours[$timeRange][] = $day;
        }

        // Move "Closed" to the end for readability
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
    public function formatConsultationDaysGroup(array $days): string
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

        // Clear cache when room changes
        static::saved(function ($room) {
            self::clearTokenCache($room->token);
        });

        static::deleted(function ($room) {
            self::clearTokenCache($room->token);
        });
    }

    /**
     * Generate cryptographically secure unique token
     */
    public static function generateSecureToken(): string
    {
        $maxAttempts = 10;
        $attempts = 0;

        do {
            $token = bin2hex(random_bytes(32)); // 64 character hex string
            $attempts++;

            if ($attempts >= $maxAttempts) {
                throw new \RuntimeException('Unable to generate unique token after ' . $maxAttempts . ' attempts');
            }
        } while (self::tokenExists($token));

        return $token;
    }

    /**
     * Check if token exists with caching
     */
    private static function tokenExists(string $token): bool
    {
        return Cache::remember("token_exists:{$token}", 300, function () use ($token) {
            return self::where('token', $token)->exists();
        });
    }

    /**
     * Validate token format (hex string, 32 characters)
     */
    public static function isValidTokenFormat(string $token): bool
    {
        return preg_match('/^[a-f0-9]{64}$/', $token) === 1;
    }

    /**
     * Find room by token with caching and validation
     */
    public static function findByValidToken(string $token): ?self
    {
        if (!self::isValidTokenFormat($token)) {
            return null;
        }

        return Cache::remember("room_token:{$token}", 600, function () use ($token) {
            return self::where('token', $token)->first();
        });
    }

    /**
     * Clear token-related cache
     */
    private static function clearTokenCache(string $token): void
    {
        Cache::forget("room_token:{$token}");
        Cache::forget("token_exists:{$token}");
    }

    /**
     * Context-aware route key selection
     * - Use tokens for public/scanner routes (secure)
     * - Use IDs for admin routes (convenient)
     */
    public function getRouteKeyName(): string
    {
        $request = request();
        $uri = $request->getRequestUri();
        $routeName = optional($request->route())->getName();

        // Use token for public/client routes
        if ($this->shouldUseToken($routeName, $uri)) {
            return 'token';
        }

        return 'id'; // Default for admin routes
    }

    private function shouldUseToken(?string $routeName, string $uri): bool
    {
        return ($routeName && str_contains($routeName, 'scan'))
            || (str_contains($uri, '/rooms/') && !str_contains($uri, '/admin/'))
            || str_contains($uri, '/api/');
    }

    /**
     * Scope for searching by name or description.
     */
    public function scopeSearch($query, ?string $term)
    {
        if (!empty($term)) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }

        return $query;
    }

    /**
     * Scope for sorting rooms by allowed columns.
     */
    public function scopeSortBy($query, ?string $column, string $direction = 'asc')
    {
        $allowed = ['id', 'name', 'description', 'room_type', 'images_count', 'created_at', 'updated_at'];

        $column = in_array($column, $allowed) ? $column : 'name';
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($column, $direction);
    }
}
