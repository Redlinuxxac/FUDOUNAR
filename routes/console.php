<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('activity:activate')->everyMinute();
Schedule::command('post:publish')->everyMinute();
Schedule::command('course:activate')->everyMinute();
