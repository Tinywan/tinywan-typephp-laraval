<?php

namespace App\Http\Controllers\User\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('mini')->user();
        return response_json('订单列表 userId = '.$request->user()->id);
    }
}
