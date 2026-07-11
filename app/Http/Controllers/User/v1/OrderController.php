<?php
/**
 * @desc WebhookController
 * @author Tinywan(ShaoBo Wan)
 * @date 2026/7/10 16:24
 */
declare(strict_types=1);

namespace App\Http\Controllers\User\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Jobs\JuBaoZan\JuBaoZanSyncUserJob;
use App\Models\Post;
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
        $params = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required',
        ],[
            'title.required' => '标题不能为空',
            'title.max' => '标题不能超过255个字符',
            'body.required' => '内容不能为空',
        ]);

        $post = new Post();
        $post->name = $params['title'];
        $post->user_id = $request->user()->id;
        $res = $post->save();
        return response_json('订单创建 userId = '.$request->user()->id,0,$params);
    }

    public function update(int $id)
    {
        $post = Post::findOrFail($id);
        $post->name = '订单更新';
        $post->save();
        return response_json('订单编辑');
    }

    public function destroy(int $id)
    {
        $post = Post::findOrFail($id);
        if (!$post) {
            return  response_json('订单不存在',404);
        }
        $post->delete();
        return response_json('订单删除');
    }
}
