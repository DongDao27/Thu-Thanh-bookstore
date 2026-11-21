<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('orders:expire --limit=100')->everyMinute()->withoutOverlapping(5);
Schedule::command('payments:reconcile --limit=20')->everyFiveMinutes()->withoutOverlapping(5);
Schedule::command('auth:clear-resets')->hourly()->withoutOverlapping(5);
Schedule::command('sanctum:prune-expired --hours=24')->daily()->withoutOverlapping(5);
Schedule::command('telescope:prune --hours=48')->daily()->withoutOverlapping(5)
    ->when(fn () => config('telescope.enabled'));
Schedule::command('ops:monitor')->everyMinute()->withoutOverlapping(2)
    ->when(fn () => config('operations.enabled'));

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
