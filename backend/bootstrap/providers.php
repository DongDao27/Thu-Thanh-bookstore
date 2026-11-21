<?php

use App\Providers\AppServiceProvider;
use App\Providers\OperationsServiceProvider;
use App\Providers\ResendServiceProvider;
use App\Providers\TelescopeServiceProvider;

return [
    AppServiceProvider::class,
    ResendServiceProvider::class,
    TelescopeServiceProvider::class,
    OperationsServiceProvider::class,
];
