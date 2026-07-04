<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user->status == 1) {
            return response_json('该用户已禁用，请联系管理员处理');
        }

        // 校验token
        $jwt = JWTAuth::parseToken()->getClaim('iat');
        return $next($request);
    }
}
