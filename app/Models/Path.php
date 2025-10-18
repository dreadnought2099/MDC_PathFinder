<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Path extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'from_room_id',
        'to_room_id',
    ];

    // Starting room
    public function fromRoom()
    {

        return $this->belongsTo(Room::class, 'from_room_id');
    }

    // Destination room
    public function toRoom()
    {

        return $this->belongsTo(Room::class, 'to_room_id');
    }

    // Images for this path
    public function images()
    {
        return $this->hasMany(PathImage::class);
    }

    /**
     * Scope: Search by fromRoom or toRoom name
     */
    public function scopeSearch($query, ?string $term)
    {
        if (!empty($term)) {
            $query->where(function ($q) use ($term) {
                $q->whereHas('fromRoom', fn($sub) => $sub->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('toRoom', fn($sub) => $sub->where('name', 'like', "%{$term}%"));
            });
        }

        return $query;
    }

    /**
     * Scope: Handle sorting logic
     */
    public function scopeSortBy($query, ?string $column, string $direction = 'asc')
    {
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        switch ($column) {
            case 'from_room':
                $query->join('rooms as from_rooms', 'paths.from_room_id', '=', 'from_rooms.id')
                    ->whereNull('from_rooms.deleted_at')
                    ->orderBy('from_rooms.name', $direction)
                    ->select('paths.*');
                break;

            case 'to_room':
                $query->join('rooms as to_rooms', 'paths.to_room_id', '=', 'to_rooms.id')
                    ->whereNull('to_rooms.deleted_at')
                    ->orderBy('to_rooms.name', $direction)
                    ->select('paths.*');
                break;

            case 'images_count':
                $query->withCount('images')->orderBy('images_count', $direction);
                break;

            default:
                $allowed = ['id', 'created_at', 'updated_at'];
                $sortColumn = in_array($column, $allowed) ? $column : 'id';
                $query->orderBy($sortColumn, $direction);
        }

        return $query;
    }
}
