<?php

namespace App\Providers;

use App\Support\TelescopeRedactor;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Telescope;

class TelescopeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Telescope::auth(fn ($request) => $request->bearerToken() === null && ($request->user('web')?->isAdmin() ?? false));
        Telescope::filter(fn ($entry) => (new TelescopeRedactor)($entry));
    }
}
