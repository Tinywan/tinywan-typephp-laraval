<?php

namespace App\Http\Controllers\User\v1;

use App\Http\Controllers\Controller;
use App\Jobs\JuBaoZan\JuBaoZanSyncUserJob;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('mini')->user();
        return response_json('订单列表 userId = '.$request->user()->id);
    }

    public function userSync(Request $request)
    {
        $user = auth('mini')->user();
        JuBaoZanSyncUserJob::dispatch($user->id, $user->shop_id);
        return response_json('用户同步',0,[
            'userId' => $user->id,
            'shopId' => $user->shop_id,
        ]);
    }

    public function create(Request $request)
    {
        return response_json('订单创建 userId = '.$request->user()->id);
    }
}
