<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-KEY');
        $validKeys = [
            '9efc314b-f7a1-4cde-a01f-738ba2a62b37',
            'd6e294e0-6ff4-441c-a55c-85b9cb5d63b1',
            'dd837b87-5612-4744-81f6-0b07aa14b9f9',
            'a3e1c964-4e59-4fc7-9ef5-5b8bb1842d7c',
            '4a9c7825-b058-41ec-b7ea-810e7773a9df',
        ];

        if (!in_array($apiKey, $validKeys)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}