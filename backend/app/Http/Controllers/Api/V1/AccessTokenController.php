<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccessTokenRequest;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AccessTokenController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return ApiResponse::data($request->user()->tokens()->latest('id')->limit(50)
            ->get(['id', 'name', 'abilities', 'last_used_at', 'expires_at', 'created_at']));
    }

    public function store(AccessTokenRequest $request): JsonResponse
    {
        $data = $request->validated();
        $token = DB::transaction(function () use ($request, $data) {
            // Serialize issuance with password recovery so a stale password cannot mint a new token.
            $user = User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            if (! Hash::check($data['password'], $user->password)) {
                throw ValidationException::withMessages(['password' => 'The password is no longer valid.']);
            }
            abort_if($user->tokens()->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))->count() >= 10, 409, 'Revoke an existing token first.');

            return $user->createToken($data['name'], $data['abilities'], now()->addDays($data['days']));
        });

        return ApiResponse::data([
            'id' => $token->accessToken->id,
            'token' => $token->plainTextToken,
            'abilities' => $token->accessToken->abilities,
            'expires_at' => $token->accessToken->expires_at,
        ], 201);
    }

    public function destroy(Request $request, string $token): Response
    {
        $request->user()->tokens()->findOrFail($token)->delete();

        return response()->noContent();
    }
}
