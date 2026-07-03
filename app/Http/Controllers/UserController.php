<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index()
    {
        return json_encode(['code' => 0, 'msg' => 'success']);
    }

    public function detail($id)
    {
        var_dump($id);
        dump($id);
        return json_encode(['code' => 0, 'msg' => 'success', 'id' => $id]);
    }

    public function export()
    {
        return Excel::download(new UsersExport, 'users.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
