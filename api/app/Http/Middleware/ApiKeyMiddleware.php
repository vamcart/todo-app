<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $headerKey = $request->header('X-API-Key');
        $expectedKey = env('API_KEY');

        if (!$headerKey || $headerKey !== $expectedKey) {
            return new JsonResponse(['message' => 'Invalid API key.'], 401);
        }

        return $next($request);
    }
}
