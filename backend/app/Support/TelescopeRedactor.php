<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Laravel\Telescope\IncomingEntry;

class TelescopeRedactor
{
    public function __invoke(IncomingEntry $entry): bool
    {
        $allowed = [
            'request' => ['method', 'response_status', 'duration', 'memory', 'controller_action'],
            'query' => ['connection', 'time', 'slow'],
            'exception' => ['class', 'file', 'line'],
            'job' => ['name', 'status', 'queue', 'connection'],
            'schedule' => ['command', 'expression', 'timezone', 'duration'],
        ];
        if (! isset($allowed[$entry->type])) {
            return false;
        }
        $entry->content = Arr::only($entry->content, $allowed[$entry->type]);
        $entry->tags = [];
        $entry->user = null;
        if ($entry->type === 'request') {
            $entry->content['uri'] = '/'.(request()->route()?->uri() ?? '[redacted]');
        }
        if ($entry->type === 'query') {
            $entry->content['sql'] = '[redacted]';
        }
        if ($entry->type === 'exception') {
            $entry->content['message'] = '[redacted]';
            $entry->content['trace'] = [];
        }

        return true;
    }
}
