<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashPaymentRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\PaymentService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class CashPaymentController extends Controller
{
    public function store(CashPaymentRequest $request, Order $order, PaymentService $payments): JsonResponse
    {
        return ApiResponse::data(new OrderResource($payments->cash($order, $request->user()->id)));
    }
}
