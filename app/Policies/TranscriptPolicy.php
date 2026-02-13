<?php

namespace App\Policies;

use App\Models\Transcript;
use App\Models\User;

class TranscriptPolicy
{
    public function manage(User $user, ?Transcript $transcript = null): bool
    {
        return (bool) $user->is_admin;
    }
}
