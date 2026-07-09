<?php

namespace App\Services;

use App\Services\JubaoZan\JuBaoZanClient;

class JuBaoZanService
{
    protected  $client;
    protected $shopId;
    public function __construct(JuBaoZanClient $client)
    {
        $this->client = $client;
        $this->shopId = $client->shopId;
    }

    public function setShopId(int $shopId): self
    {
        $this->shopId = $shopId;
        return $this;
    }

    public function getShopId(): ?int
    {
        return $this->shopId;
    }

    public function getClient(): JuBaoZanClient
    {
        return $this->client;
    }

    public function createUser(UserCreateBean $userBean): array
    {
        return  [];
    }

    public function updateMobile($agentId, $mobile): array
    {
        return  [];
    }

    public function queryUserByUnionid(string $unionid): array
    {
//        $queryBean = new UserQueryBean();
//        $queryBean->unionid = $unionid;
//        return $this->queryUser($queryBean);
        return  [];
    }
}
