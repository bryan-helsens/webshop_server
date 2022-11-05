<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JWTAuthentication
{
    public function handle(Request $request, Closure $next)
    {

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            if ($e instanceof TokenExpiredException) {
                $newToken = JWTAuth::parseToken()->refresh();

                return response()->json([
                    'status' => 'error',
                    'access_token' => $newToken,
                    'message' => 'Token Expired',
                ], 200);
            } else if ($e instanceof TokenInvalidException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token Invalid',
                ], 401);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token Not Found',
                ], 401);
            }
        }

        return $next($request);
    }
}
