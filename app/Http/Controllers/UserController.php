<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Jobs\HelloJob;
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
        HelloJob::dispatch($userId)->delay(30);
        return json_encode(['code' => 0, 'msg' => 'success']);
    }
}
