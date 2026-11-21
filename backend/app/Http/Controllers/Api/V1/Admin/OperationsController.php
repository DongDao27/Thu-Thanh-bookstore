<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperationalAlert;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class OperationsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return ApiResponse::data([
            'enabled' => (bool) config('operations.enabled'),
            'host' => Cache::get('ops.host_snapshot'),
            'alerts' => config('operations.enabled') ? OperationalAlert::latest('id')->limit(50)->get(['id', 'code', 'context', 'created_at']) : [],
        ]);
    }
}
