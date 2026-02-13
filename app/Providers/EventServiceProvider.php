<?php

namespace App\Providers;

use App\Events\ClipSaved;
use App\Events\UserLoggedIn;
use App\Events\UserRegistered;
use App\Events\VideoUploaded;
use App\Listeners\LogClipSaved;
use App\Listeners\LogUserLogin;
use App\Listeners\LogVideoUpload;
use App\Listeners\SendWelcomeNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserRegistered::class => [
            SendWelcomeNotification::class,
        ],
        UserLoggedIn::class => [
            LogUserLogin::class,
        ],
        VideoUploaded::class => [
            LogVideoUpload::class,
        ],
        ClipSaved::class => [
            LogClipSaved::class,
        ],
    ];
}
