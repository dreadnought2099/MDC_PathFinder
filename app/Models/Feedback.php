<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'message',
        'rating',
        'feedback_type',
        'page_url',
        'ip_hash',
        'recaptcha_score',
        'status'
    ];

    protected $casts = [
        'recaptcha_score' => 'float',
        'rating' => 'integer',
    ];

    // Scope to get recent feedback
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Scope to get pending feedback
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
