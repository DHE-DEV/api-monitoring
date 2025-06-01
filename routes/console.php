<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Laravel 12 is ready!');
})->purpose('Display a message');
