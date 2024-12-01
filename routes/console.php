<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\ProcessPodcast;
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
Schedule::command('app:daily-cleanup')->dailyAt('00:00');

Schedule::call(function () {
    $podcast = "bhawani";
    ProcessPodcast::dispatch($podcast);
})->everyMinute();
