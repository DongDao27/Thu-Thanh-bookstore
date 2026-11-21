<?php

namespace App\Observers;

use App\Events\OrderChanged;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderRealtimeObserver
{
    public function updated(Order $order): void
    {
        if (config('broadcasting.default') !== 'pusher'
            || ! $order->wasChanged(['lifecycle', 'payment_status', 'fulfillment_status', 'version'])) {
            return;
        }

        $event = new OrderChanged($order->order_code, $order->version);
        // Only enqueue after commit; queue outages must not turn a committed payment into an HTTP error.
        DB::afterCommit(fn () => rescue(fn () => OrderChanged::dispatch($event->code, $event->version)));
    }
}
