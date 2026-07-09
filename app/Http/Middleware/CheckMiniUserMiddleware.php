<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckMiniUserMiddleware
{
    public $shopId = null;

    public $shop = null;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $appId = $request->header('appid');
        if (!$appId) {
            return response_json('appid is required', 400);
        }

        $shop = Cache::remember('USER:MINI:'.$appId, 36000, function () use ($appId) {
            return Shop::query()->where('app_id', $appId)->first();
        });
        if (!$shop) {
            return response()->json(['error' => '小程序不存在'], 401);
        }

        $request->merge(['shop' => $shop]);

        return $next($request);
    }

    public function initialize()
    {
        if ($this->shopId) {
            $this->shop = Shop::query()->findOrFail($this->shopId);
        }
    }
}
