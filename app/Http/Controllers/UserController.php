<?php

namespace App\Http\Controllers;

use App\Events\GoodsSkuEvent;
use App\Events\UserCreatedEvent;
use App\Exports\UsersExport;
use App\Jobs\JuBaoZan\SyncUserJob;
use App\Models\GoodsSkuModel;
use App\Models\Post;
use App\User;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index()
    {
        return json_encode(['code' => 0, 'msg' => 'success']);
    }

    public function detail(int $id,string $name)
    {
        var_dump($id);
        var_dump($name);
        dump($name);
        dump($id);
        return json_encode(['code' => 0, 'msg' => 'success', 'id' => $id]);
    }

    public function export()
    {
        return Excel::download(new UsersExport, 'users.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function queue()
    {
        $userId = 10086;
        $shopId = 10;
        Log::error('同步用户开始 2026 ', ['userId' => $userId, 'shopId' => $shopId]);
//        $a = 1/0;
        SyncUserJob::dispatch($userId,$shopId)->delay(30);
        return json_encode(['code' => 0, 'msg' => 'success']);
    }

    public function event()
    {
        $user = User::findOrFail(1);
        // 用户登录事件
//        event(new \App\Events\UserCreatedEvent($user));
        event(new \App\Events\UserLoginEvent($user));
        return json_encode(['code' => 0, 'msg' => 'event success']);
    }

    public function sku()
    {
        // 通过名称检索，或使用名称、延迟和到达时间属性创建它...
        $update1 = Post::query()->firstOrCreate(
            ['author' => 'Tinyaiai'],
            ['name' => '开源技术小栈', 'author' => 'Tinyaiai','status' => 1]
        );
        $skuId = 480;
        // SKU 库存更新
        $goodsSukModel = GoodsSkuModel::query()->where('id',$skuId)->first();
        event(new GoodsSkuEvent($goodsSukModel));
        return json_encode(['code' => 0, 'msg' => 'success']);
    }
}
