<?php

namespace App\Services\JubaoZan;

use App\User;
use App\Services\JuBaoZanService;
use Illuminate\Support\Facades\Log;
use Exception;
class JuBaoZanUserSyncService
{
    protected $service;

    public function __construct(JuBaoZanService $service)
    {
        $this->service = $service;
    }

    public function syncUser(User $user): array
    {
        if (!$user->mobile && !$user->unionid_open) {
            throw new Exception('User must have mobile or unionid');
        }
        Log::info('[Service] [用户同步] [聚宝赞] 开始同步用户', ['user_id' => $user->id]);
        return [];
    }
}
