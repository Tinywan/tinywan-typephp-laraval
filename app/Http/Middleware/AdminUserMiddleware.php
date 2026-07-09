<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminUserMiddleware
{
    private $user;

    private $role;

    private $shopId = null;

    private $shop = null;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //$user = auth('admin')->user();
        $user = $request->user();
        if ($user->status == 1) {
            return response_json('该用户已禁用，请联系管理员处理');
        }

        $this->shopId = $request->header('Shop-Id');
        // 校验token
        // $jwt = JWTAuth::parseToken()->getClaim('iat');

        $this->initialize();

        $request->merge([
            'x-site-domain' => 'shop.tinywan.com',
            'x-site-code' => 'shop',
            'shop' => $this->shop,
            'shop_id' => $this->shopId,
        ]);

        return $next($request);
    }

    public function initialize()
    {
        if ($this->shopId) {
            $this->shop = Shop::query()->findOrFail($this->shopId);
        }
    }
}
