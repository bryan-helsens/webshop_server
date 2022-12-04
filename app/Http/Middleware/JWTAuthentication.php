<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Auth\AuthManager;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTAuthentication
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        try {
            if ($request->cookie('jwt')) {
                $request->headers->set('Authorization', 'Bearer ' . $request->cookie('jwt'));
            }

            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            if ($e instanceof TokenExpiredException) {


                try {
                    $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                    $user = JWTAuth::setToken($refreshed)->toUser();
                    $request->headers->set('Authorization', 'Bearer ' . $refreshed);

                    return $next($request)->withCookie(cookie("jwt", $refreshed, auth()->factory()->getTTL()));
                } catch (JWTException $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Token cannot be refreshed, please login again"
                    ], 103);
                }
            } else if ($e instanceof TokenInvalidException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token Invalid, re-login again',
                ], 401);
            } else if ($e instanceof TokenBlacklistedException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token has been blacklisted, re-login again',
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
