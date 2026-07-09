<?php

namespace App\Jobs\JuBaoZan;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected  $userId;

    protected  $shopId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId,$shopId)
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
        // 执行作业
        Log::info('[用户同步] [聚宝赞] 执行作业....'.json_encode($this->userId, JSON_UNESCAPED_UNICODE).', shopId: '.$this->shopId);
    }
}
