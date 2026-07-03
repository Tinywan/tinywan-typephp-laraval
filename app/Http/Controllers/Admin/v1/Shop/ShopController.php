<?php

namespace App\Http\Controllers\Admin\v1\Shop;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class ShopController extends AdminController
{
    public function index(Request $request)
    {
        return response_json('这是商品列表');
    }

    public function detail($id)
    {
        return response_json('这是商品详情', 0, ['id' => $id]);
    }
}
