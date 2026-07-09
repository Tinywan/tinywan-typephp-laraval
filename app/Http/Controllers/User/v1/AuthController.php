<?php

namespace App\Http\Controllers\User\v1;

use App\Events\UserCreatedEvent;
use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        /** @var Shop $shop */
        $shop = $request->get('shop');
        $openid = 'o7R6n4o0kq7BKTWhUPk5a-srKhxk';
        $user = User::query()
            ->where('shop_id', $shop->id)
            ->where('openid', $openid)
            ->first();
        /** 生成登录token */
        $ttl = 14 * 24 * 60;
        $token = auth('mini')->setTTL($ttl)->login($user);

        /** 触发登录后事件 */
        event(new UserCreatedEvent($user, $shop));
        return response_json('登录成功', 0, [
            'access_token' => $token ,
            'token_type' => 'Bearer',
            'expires_in' => $ttl * 60
        ]);
    }
}
