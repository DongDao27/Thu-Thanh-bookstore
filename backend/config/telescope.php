<?php

use App\Http\Middleware\RequireAdminSession;
use Laravel\Telescope\Watchers;

return [
    // Opt in only after the additive migrations have run for that environment.
    'enabled' => env('TELESCOPE_ENABLED', false),
    'path' => 'telescope',
    'driver' => 'database',
    'storage' => ['database' => ['connection' => env('DB_CONNECTION', 'pgsql'), 'chunk' => 100]],
    'middleware' => ['web', RequireAdminSession::class],
    'watchers' => [
        Watchers\RequestWatcher::class => true,
        Watchers\QueryWatcher::class => ['enabled' => true, 'slow' => 100],
        Watchers\ExceptionWatcher::class => true,
        // Failed-job updates bypass entry filters and can contain raw exception details.
        Watchers\JobWatcher::class => false,
        Watchers\ScheduleWatcher::class => true,
    ],
];
