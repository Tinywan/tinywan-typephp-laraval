<?php

namespace App\Listeners;

use App\Events\GoodsSkuEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateErpGoodsStockListener implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GoodsSkuEvent $event)
    {
        Log::info('【ER商品库存更新】执行作业....',[$event->goodsSkuModel]);
        $goodsSkuModel = $event->goodsSkuModel;
        $goodsSkuModel->available_inventory = 480;
        $goodsSkuModel->actual_inventory = 480;
        $result = $goodsSkuModel->save();
        Log::info('【ER商品库存更新】执行作业保存结果....'.json_encode($result,JSON_UNESCAPED_UNICODE));
    }
}
