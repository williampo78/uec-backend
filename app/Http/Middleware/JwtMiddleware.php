<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    public function __construct()
    {
        config(['auth.defaults.guard' => 'api']);
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (!$member = JWTAuth::parseToken()->authenticate()) {
                JWTAuth::parseToken()->invalidate();

                return response()->json([
                    'message' => '會員不存在',
                ], 404);
            }
        } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json([
                    'message' => '無效的token',
                ], 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                try {
                    $token = JWTAuth::parseToken()->refresh();
                    Auth::guard('api')->onceUsingId(JWTAuth::parseToken()->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
                    return $this->setAuthenticationHeader($next($request), $token);
                } catch (JWTException $exception) {
                    return response()->json([
                        'message' => 'token已過期',
                    ], 401);
                }
            } else {
                return response()->json([
                    'message' => 'token不存在',
                ], 404);
            }
        }

        return $next($request);
    }
}
