<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function index()
    {
        return json_encode(['code' => 0, 'msg' => 'success']);
    }

    public function detail($id)
    {
        return json_encode(['code' => 0, 'msg' => 'success', 'id' => $id]);
    }
}
