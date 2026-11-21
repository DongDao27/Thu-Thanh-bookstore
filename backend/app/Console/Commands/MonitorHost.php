<?php

namespace App\Console\Commands;

use App\Services\HostMonitor;
use App\Services\OperationalAlerts;
use Illuminate\Console\Command;
use Throwable;

class MonitorHost extends Command
{
    protected $signature = 'ops:monitor';

    protected $description = 'Sample real host metrics and alert on configured thresholds';

    public function handle(HostMonitor $monitor, OperationalAlerts $alerts): int
    {
        if (! config('operations.enabled')) {
            $this->error('Set OPS_MONITORING_ENABLED only after operational migrations and collector setup.');

            return self::FAILURE;
        }
        try {
            $metrics = $monitor->sample();
            $breaches = $monitor->check($metrics);
            $this->line(json_encode(['metrics' => $metrics, 'breaches' => $breaches], JSON_THROW_ON_ERROR));

            return $breaches === [] ? self::SUCCESS : self::FAILURE;
        } catch (Throwable $exception) {
            $alerts->record('monitor_failure', ['exception_class' => $exception::class]);
            $this->error('Host collection failed; check the Python binary and psutil installation.');

            return self::FAILURE;
        }
    }
}
