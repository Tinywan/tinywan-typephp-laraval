<?php

namespace App\Listeners;

use App\Events\UserCreatedEvent;
use App\Models\JuBaoZanAccessToken;
use App\Services\JubaoZan\JuBaoZanClient;
use App\Services\JubaoZan\JuBaoZanUserSyncService;
use App\Services\JuBaoZanService;
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
        Log::info('[Listener] [聚宝赞用户同步] 监听用户创建事件', ['user' => $user->nickname, 'shop' => $shop->id]);

        // 检查店铺是否在白名单中
        $client = new JuBaoZanClient($shop->id);
        if (!in_array($shop->id, $client->whiteShopIds)) {
            return;
        }

        /** 检查店铺是否有聚宝赞授权 */
        $accessToken = JuBaoZanAccessToken::query()
            ->where('shop_id', $shop->id)
            ->where('status', true)
            ->first();
        if (!$accessToken) {
            return;
        }

        try {
            // 2. 初始化服务并同步用户
            $jbzService = (new JuBaoZanService($client))->setShopId($shop->id);
            $userSyncService = new JuBaoZanUserSyncService($jbzService);
            $userSyncService->syncUser($user);
        } catch (\Exception $e) {
            Log::error('处理聚宝赞用户创建事件失败', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }
    }
}
