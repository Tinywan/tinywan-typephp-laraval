<?php

namespace App\Listeners;

use App\Events\UserLoginEvent;
use App\Events\UserLogoutEvent;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

class UserEventSubscriber
{
    /**
     * 处理用户登录事件。
     */
    public function handleUserLogin($event) {
        Log::info('[事件订阅] User logged out:', ['user_id' => $event->user->id]);
    }

    /**
     * 处理用户注销事件。
     */
    public function handleUserLogout($event) {
        Log::info('[事件订阅] User logged out:', ['user_id' => $event->user->id]);
    }

    /**
     * 为订阅者注册监听器。
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            UserLoginEvent::class,
            'App\Listeners\UserEventSubscriber@handleUserLogin'
        );

        $events->listen(
            UserLogoutEvent::class,
            'App\Listeners\UserEventSubscriber@handleUserLogout'
        );
    }
}
