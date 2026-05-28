<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// GDPR retention sweep — runs daily at 03:00
Schedule::command('bugs:retention-cleanup')->dailyAt('03:00')->withoutOverlapping();

// GA4 metrics sync — runs on day 1 at 04:00 (previous month's data) + daily refresh of current month at 04:30
Schedule::command('metrics:sync-ga4')->monthlyOn(1, '04:00')->withoutOverlapping();
Schedule::command('metrics:sync-ga4 --month=' . now()->format('Y-m'))->dailyAt('04:30')->withoutOverlapping();

// GSC keyword sync — staggered 30 min after GA4 so they don't fight for service-account quota
Schedule::command('metrics:sync-gsc')->monthlyOn(1, '04:30')->withoutOverlapping();
Schedule::command('metrics:sync-gsc --month=' . now()->format('Y-m'))->dailyAt('05:00')->withoutOverlapping();

// GBP sync — staggered after GSC
Schedule::command('metrics:sync-gbp')->monthlyOn(1, '05:00')->withoutOverlapping();
Schedule::command('metrics:sync-gbp --month=' . now()->format('Y-m'))->dailyAt('05:30')->withoutOverlapping();
