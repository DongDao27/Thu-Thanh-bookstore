<?php

namespace App\Http\Controllers\Api\V1;

use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class BroadcastAuthController
{
    public function __invoke(Request $request): mixed
    {
        abort_unless(config('broadcasting.default') === 'pusher', 503);
        $request->validate([
            'socket_id' => ['required', 'string', 'max:100', 'regex:/\A[0-9]+\.[0-9]+\z/'],
            'channel_name' => ['required', 'string', 'max:100', 'regex:/\Aprivate-orders\.[A-Za-z0-9-]+\z/'],
        ]);

        return ApiResponse::data(Broadcast::auth($request));
    }
}
