<?php

namespace App\Providers;

use App\Models\Transcript;
use App\Models\Video;
use App\Policies\TranscriptPolicy;
use App\Policies\VideoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Video::class => VideoPolicy::class,
        Transcript::class => TranscriptPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('access-admin', function ($user): bool {
            return (bool) $user?->is_admin;
        });
    }
}
