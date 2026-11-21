<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IntegrationAccountController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return ApiResponse::data($request->user()->only(['id', 'name', 'role']));
    }
}
