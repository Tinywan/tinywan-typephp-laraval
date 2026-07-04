<?php

namespace App\Policies;

use App\Models\Post;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the post model.
     *
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;

//        return $user->id === $post->user_id ? Response::allow()
//            : Response::deny('您不拥有此帖子。');
    }

}
