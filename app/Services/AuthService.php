<?php

namespace App\Services;

use App\Events\UserLoggedIn;
use App\Events\UserRegistered;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        event(new UserRegistered($user));

        return $this->issueTokens($user);
    }

    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        event(new UserLoggedIn($user));

        return $this->issueTokens($user);
    }

    public function refresh(string $refreshToken): array
    {
        $tokenHash = hash('sha256', $refreshToken);
        $stored = RefreshToken::where('token_hash', $tokenHash)->first();

        if (! $stored || $stored->isExpired()) {
            throw ValidationException::withMessages([
                'refresh_token' => ['Refresh token expired or invalid.'],
            ]);
        }

        $stored->update(['last_used_at' => now()]);
        $stored->revoke();

        Log::info('Refresh token rotated', [
            'user_id' => $stored->user_id,
            'refresh_token_id' => $stored->id,
        ]);

        return $this->issueTokens($stored->user);
    }

    public function logout(User $user, ?PersonalAccessToken $accessToken): void
    {
        if ($accessToken) {
            $accessToken->delete();
        }

        RefreshToken::where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
    }

    private function issueTokens(User $user): array
    {
        $accessToken = $user->createToken('mobile')->plainTextToken;
        $refreshToken = RefreshToken::issueForUser($user);

        return [
            'user' => $user,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken->plainText,
        ];
    }
}
