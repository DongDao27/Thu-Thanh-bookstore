<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireSessionAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        // Scoped external tokens must never reach the broader first-party API.
        abort_if($request->bearerToken() !== null, 403, 'Use the scoped integration API.');

        return $next($request);
    }
}
