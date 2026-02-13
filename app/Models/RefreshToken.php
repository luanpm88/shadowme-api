<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RefreshToken extends Model
{
    use HasFactory;

    public string $plainText;

    protected $fillable = [
        'user_id',
        'token_hash',
        'expires_at',
        'last_used_at',
        'revoked_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function issueForUser(User $user): self
    {
        $plainText = Str::random(64);

        $token = self::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plainText),
            'expires_at' => now()->addDays(30),
        ]);

        $token->plainText = $plainText;

        return $token;
    }

    public function isExpired(): bool
    {
        return $this->revoked_at !== null || $this->expires_at->isPast();
    }

    public function revoke(): void
    {
        $this->update(['revoked_at' => now()]);
    }
}
