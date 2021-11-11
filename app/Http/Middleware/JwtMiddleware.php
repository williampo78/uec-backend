<?php

namespace App\Http\Middleware;

use App\Models\Members;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Log;

class JwtMiddleware
{
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
            if ($request->bearerToken()) {
                $token = $request->bearerToken();
                $member = Members::where('api_token', '=', $token)->get()->toArray();
                if (sizeof($member) >0) {
                    $request->merge(array("member" => $member[0]['member_id']));
                } else {
                    Log::info($request);
                    return response()->json(['status' => false, 'error_code' => '202', 'error_msg' => '無效的Token', 'result' => []]);
                }
            } else {
                Log::info($request);
                return response()->json(['status' => false, 'error_code' => '202', 'error_msg' => '無效的Token', 'result' => []]);
            }
            //$member = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            Log::info($e);
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status' => 'Token is Invalid']);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['status' => 'Token is Expired']);
            } else {
                return response()->json(['status' => false, 'error_code' => '202', 'error_msg' => '無效的Token', 'result' => []]);
            }
        }

        return $next($request);
    }
}
