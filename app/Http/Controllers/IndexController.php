<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IndexController extends Controller
{
    /**
     * JSON 格式响应测试
     */
    public function json(Request $request): JsonResponse
    {
        return response()->json([
            'code' => 0,
            'msg' => 'ok',
            'data' => [
                'engine' => 'TypePHP AOT Compiler',
                'framework' => 'Laravel 13',
                'timestamp' => time(),
                'datetime' => date('Y-m-d H:i:s'),
                'params' => $request->all(),
            ],
        ]);
    }

    /**
     * 欢迎页面渲染测试
     */
    public function welcome(Request $request): View
    {
        return view('welcome');
    }

    /**
     * 算术与系统性能计算测试
     */
    public function benchmark(Request $request): JsonResponse
    {
        $start = microtime(true);
        $total = 0;
        for ($i = 0; $i < 100000; $i++) {
            $total += ($i * 2) - ($i / 2);
        }
        $cost = round((microtime(true) - $start) * 1000, 3);

        return response()->json([
            'code' => 0,
            'msg' => 'benchmark finished',
            'data' => [
                'iterations' => 100000,
                'cost_ms' => $cost,
                'result' => $total,
            ],
        ]);
    }
}
