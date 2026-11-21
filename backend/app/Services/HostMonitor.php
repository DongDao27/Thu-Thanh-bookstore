<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;

class HostMonitor
{
    public function sample(): array
    {
        $process = new Process([config('operations.python'), base_path('../scripts/monitor-host.py')]);
        $process->setTimeout(15)->mustRun();
        $metrics = json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR);
        foreach (config('operations.thresholds') as $key => $threshold) {
            if (! isset($metrics[$key]) || ! is_numeric($metrics[$key]) || $metrics[$key] < 0) {
                throw new \RuntimeException('Host collector returned invalid metrics.');
            }
        }

        return $metrics;
    }

    public function check(array $metrics): array
    {
        $breaches = [];
        foreach (config('operations.thresholds') as $key => $threshold) {
            if ($metrics[$key] >= $threshold) {
                $breaches[] = $key;
                app(OperationalAlerts::class)->record('host_'.$key, ['metric' => $key, 'value' => $metrics[$key], 'threshold' => $threshold]);
            }
        }
        Cache::put('ops.host_snapshot', ['metrics' => $metrics, 'sampled_at' => now()->toISOString(), 'breaches' => $breaches], now()->addMinutes(5));

        return $breaches;
    }
}
