<?php

namespace App\Listeners;

use App\Events\UserCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class JuBaoZanUserCreatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param UserCreatedEvent $event
     * @return void
     */
    public function handle(UserCreatedEvent  $event)
    {
        $user = $event->user;
        $shop = $event->shop;
        Log::info('[聚宝赞用户同步] 监听用户创建事件', ['user' => $user->nickname, 'shop' => $shop->name]);
    }
}
