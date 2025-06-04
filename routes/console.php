<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Monitor;
use App\Jobs\ExecuteMonitorJob;

//Schedule::command('monitor:run')->everyMinute();
Schedule::command('api:monitor')->everyMinute();

Artisan::command('inspire', function () {
    $this->comment('Laravel 12 is ready!');
})->purpose('Display a message');
