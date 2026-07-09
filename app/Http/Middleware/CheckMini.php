<?php

namespace App\Http\Middleware;

use App\Enum\Cache\CacheKeyEnum;
use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CheckMini
{
    // 不运营的小程序
    private $notMiniAppIds = [
        '' => ''
    ];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 校验 小程序appid
        $appId = $request->header('appid');
        if (!$appId) {
            return response()->json(['error' => '小程序app id 不存在'], 401);
        }
        if (isset($this->notMiniAppIds[$appId])) {
            return response()->json(['error' => $this->notMiniAppIds[$appId]], 401);
        }

        // 店铺小程序是否存在 查询太多 缓存一下减少数据库查询次数
        $tagKey = 'USER:MINI:'.$appId;
        $shop = Cache::remember($tagKey, 36000, function () use ($appId) {
            return Shop::query()
                ->where('app_id', $appId)->first();
        });
        if (!$shop) {
            return response()->json(['error' => '小程序不存在'], 401);
        }

        // 合并店铺参数
        $request->merge(['shop' => $shop]);

        return $next($request, $shop);
    }
}
