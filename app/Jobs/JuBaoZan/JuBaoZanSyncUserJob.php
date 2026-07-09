<?php

namespace App\Jobs\JuBaoZan;

use App\Services\JubaoZan\JuBaoZanUserSyncService;
use App\Services\JuBaoZanService;
use App\User;
use App\Services\JubaoZan\JuBaoZanClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class JuBaoZanSyncUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    protected $shopId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $shopId)
    {
        $this->userId = $userId;
        $this->shopId = $shopId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('[JOB] [用户同步] [聚宝赞] 执行作业....', ['user_id' => $this->userId, 'shop_id' => $this->shopId]);

        $client = new JuBaoZanClient($this->shopId);
        if (!in_array($this->shopId, $client->whiteShopIds)) {
            return;
        }

        $user = User::find($this->userId);
        if (!$user) {
            Log::error('[JOB] [用户同步] [聚宝赞] 用户不存在', ['user_id' => $this->userId]);
            return;
        }
        Log::info('[JOB] [用户同步] [聚宝赞] 执行服务类', ['user_id' => $this->userId]);
        $service = new JuBaoZanUserSyncService(new JuBaoZanService($client));
        $service->syncUser($user);
        Log::info('[JOB] [用户同步] [聚宝赞] 用户同步成功', ['user_id' => $this->userId]);
    }
}
