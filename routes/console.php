<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automatically apply late payment penalties daily at midnight
Schedule::command('invoices:apply-penalties')
    ->daily()
    ->at('00:01')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/penalties.log'));
