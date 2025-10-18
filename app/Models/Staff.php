<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

        static::saving(function ($staff) {
            $staff->full_name = trim(
                $staff->first_name . ' ' .
                    ($staff->middle_name ? $staff->middle_name . ' ' : '') .
                    $staff->last_name .
                    ($staff->suffix ? ' ' . $staff->suffix : '')
            );
        });
    }

    /**
     * Scope: Search staff by name or email
     */
    public function scopeSearch($query, ?string $term)
    {
        if (!empty($term)) {
            $query->where(function ($q) use ($term) {
                $q->where('full_name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('position', 'like', "%{$term}%");
            });
        }

        return $query;
    }

    /**
     * Scope: Sort by allowed columns
     */
    public function scopeSortBy($query, ?string $column, string $direction = 'asc')
    {
        $allowed = ['id', 'full_name', 'last_name', 'email', 'position', 'created_at', 'updated_at'];

        if (in_array($column, $allowed)) {
            return $query->orderBy($column, $direction);
        }

        // Default fallback
        return $query->orderBy('full_name', 'asc');
    }
}
