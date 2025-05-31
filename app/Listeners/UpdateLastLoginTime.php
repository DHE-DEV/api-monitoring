<?php

namespace App\Listeners;

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateLastLoginTime
{
    public function __construct()
    {
        //
    }

    public function handle(Login $event): void
    {
        $event->user->updateLastLogin();
    }
}
