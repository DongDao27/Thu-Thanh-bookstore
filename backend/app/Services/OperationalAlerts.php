<?php

namespace App\Services;

use App\Models\OperationalAlert;
use App\Notifications\OperationalAlertNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class OperationalAlerts
{
    private bool $recording = false;

    public function record(string $code, array $context = []): void
    {
        if (! config('operations.enabled') || $this->recording) {
            return;
        }
        $this->recording = true;
        try {
            if (! Cache::add('ops-alert:'.$code, true, config('operations.alert_cooldown_seconds'))) {
                return;
            }
            $safe = Arr::only($context, ['metric', 'value', 'threshold', 'exception_class', 'sql_hash', 'duration_ms', 'connection']);
            OperationalAlert::create(['code' => $code, 'context' => $safe]);
            Log::warning('Operational alert', ['code' => $code, ...$safe]);
            if ($email = config('operations.alert_email')) {
                Notification::route('mail', $email)->notify(new OperationalAlertNotification($code));
            }
        } catch (Throwable $exception) {
            // Observability must not turn a database/transport outage into recursive failures.
            Log::warning('Operational alert storage unavailable', ['code' => $code, 'exception_class' => $exception::class]);
        } finally {
            $this->recording = false;
        }
    }
}
