<?php

namespace App\Http\Controllers\Admin\v1;

use App\Http\Controllers\AdminController;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class PostController extends AdminController
{
    /**
     * 更新给定的博客帖子。
     *
     * @param int $id
     * @return JsonResponse
     */
    public function update(int $id)
    {
//        dump(\request()->get('x-site-domain'));
//        dump(\request()->get('x-site-code'));
        $post = Post::findOrFail($id);
        $this->authorize('update', $post);

        // 当前用户可以更新博客帖子...
        return response_json('更新成功');
    }
}
