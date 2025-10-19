<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule task escalation processing to run every hour
Schedule::command('tasks:escalate')->everyMinute()->withoutOverlapping();

// Schedule reminder processing to run every 15 minutes
Schedule::command('tasks:reminders')->everyMinute()->withoutOverlapping();