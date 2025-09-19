<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'room_id',
        'is_active',
        'google2fa_secret',   // Required so we can save the user's secret key generated for Google Authenticator
        'two_factor_recovery_codes',   // Required so we can save hashed recovery codes into DB
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',      // Hide the Google Authenticator secret for security
        'two_factor_recovery_codes',    //  Hide the recovery codes to prevent leaking them in responses
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_recovery_codes' => 'array',
        ];
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }


    /**
     * Generate one-time recovery codes, hash them for storage,
     * and return the plain versions to show to the user.
     */
    public function generateRecoveryCodes(int $count = 10, int $length = 10): array
    {
        $plain = [];
        $hashed = [];

        for ($i = 0; $i < $count; $i++) {
            $code = strtoupper(Str::random($length));
            $plain[] = $code;
            $hashed[] = Hash::make($code);
        }

        $this->update([
            'two_factor_recovery_codes' => $hashed
        ]);

        return $plain; // plain codes shown ONCE to user
    }

    /**
     * Verify and consume a recovery code (case-insensitive).
     */
    public function verifyRecoveryCode(string $code): bool
    {
        $codes = $this->two_factor_recovery_codes ?? [];
        $code = strtoupper(trim($code)); // normalize

        foreach ($codes as $i => $hashed) {
            if (Hash::check($code, $hashed)) {
                // remove used code and persist remaining hashed codes
                unset($codes[$i]);
                $this->update(['two_factor_recovery_codes' => array_values($codes)]);
                return true;
            }
        }

        return false;
    }

    /**
     * Remove all recovery codes.
     */
    public function clearRecoveryCodes(): void
    {
        $this->update(['two_factor_recovery_codes' => []]);
    }
}
