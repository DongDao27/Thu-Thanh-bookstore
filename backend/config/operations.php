<?php

return [
    'enabled' => env('OPS_MONITORING_ENABLED', false),
    'slow_query_ms' => (float) env('OPS_SLOW_QUERY_MS', 500),
    'alert_cooldown_seconds' => 300,
    'alert_email' => env('OPS_ALERT_EMAIL'),
    'python' => env('OPS_PYTHON_BINARY', PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3'),
    'thresholds' => [
        'cpu_percent' => (float) env('OPS_CPU_PERCENT', 90),
        'memory_percent' => (float) env('OPS_MEMORY_PERCENT', 90),
        'disk_percent' => (float) env('OPS_DISK_PERCENT', 90),
        'network_bytes_per_second' => (float) env('OPS_NETWORK_BYTES_PER_SECOND', 125000000),
    ],
];
