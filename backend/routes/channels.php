<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('orders.{code}', function (User $user, string $code): bool {
    return Order::where('order_code', $code)
        ->when(! $user->isAdmin(), fn ($query) => $query->where('user_id', $user->id))
        ->exists();
}, ['guards' => ['web']]);
