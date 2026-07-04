<?php

namespace App\Http\Controllers\Admin\v1\Shop;

use App\Http\Controllers\AdminController;
use App\Http\Requests\UserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ShopController extends AdminController
{
    public function index(Request $request)
    {
        return response_json('这是商品列表');
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function detail($id)
    {
        Log::info('Showing the user profile for user: ', ['id' => $id]);
        Log::info('User failed to login.', ['id' => $id]);
        return response_json('这是商品详情', 0, ['id' => $id]);
    }

    /**
     * 存储新的商品
     * @return JsonResponse
     */
    public function student()
    {
        $validated = \request()->validate([
            'id' => 'required',
            'age' => 'required|integer',
            'name' => 'required|max:255',
        ]);
        return response_json('存储新的商品');
    }

    /**
     * 存储新的商品
     * @return JsonResponse
     */
    public function teacher(UserRequest $request)
    {
        Log::debug('An informational message. debug ');
        Log::info('An informational message. info ');
        Log::error('An informational message.  error');
        $validated = $request->validated();
        return response_json('老师验证器', 0, $validated);
    }
}
