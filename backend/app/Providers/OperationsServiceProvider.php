<?php

namespace App\Providers;

use App\Services\OperationalAlerts;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class OperationsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Singleton protects the query listener from recursively observing its own alert writes.
        $this->app->singleton(OperationalAlerts::class);
    }

    public function boot(): void
    {
        DB::listen(function (QueryExecuted $query): void {
            if ($query->time >= config('operations.slow_query_ms')) {
                app(OperationalAlerts::class)->record('slow_query', [
                    'sql_hash' => hash('sha256', $query->sql),
                    'duration_ms' => $query->time,
                    'connection' => $query->connectionName,
                ]);
            }
        });
    }
}
