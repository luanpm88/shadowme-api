<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;

class VideoPolicy
{
    public function manage(User $user, ?Video $video = null): bool
    {
        return (bool) $user->is_admin;
    }

    public function view(?User $user, Video $video): bool
    {
        return $video->is_published || (bool) ($user?->is_admin);
    }
}
