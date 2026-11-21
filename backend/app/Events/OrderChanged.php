<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class OrderChanged implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable;

    public int $tries = 3;

    public int $timeout = 15;

    public array $backoff = [5, 30, 120];

    public function __construct(public string $code, public int $version) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('orders.'.$this->code)];
    }

    public function broadcastAs(): string
    {
        return 'order.changed';
    }

    public function broadcastWith(): array
    {
        // Invalidation only: never send PII, amounts, tokens or infer paid in the client.
        return ['order_code' => $this->code, 'version' => $this->version];
    }

    public function broadcastWhen(): bool
    {
        return config('broadcasting.default') === 'pusher';
    }
}
