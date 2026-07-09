<?php

namespace App\Http\Controllers;

use App\Events\UserCreatedEvent;
use App\Exports\UsersExport;
use App\Jobs\JuBaoZan\SyncUserJob;
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
}
