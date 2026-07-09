<?php

namespace App\Services\JubaoZan;

use GuzzleHttp\Client;

class JuBaoZanClient
{
    protected $client;
    public $shopId;
    protected $host;
    protected $clientId;
    protected $clientSecret;
    protected $accessTokenKey;
    public $whiteShopIds;

    public function __construct($shopId)
    {
        $this->client = new Client([
            'timeout' => 30,
            'connect_timeout' => 10,
        ]);
        $this->shopId = $shopId;
        $this->initialize();
    }

    public function initialize()
    {
        $config = config('services.jubaozan');
        $this->host = $config['host'];
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->accessTokenKey = 'jubaozan_access_token_' . $this->clientId . '_' . $this->shopId;
        $this->whiteShopIds = $config['white_shop_ids'];
    }

    public function getAccessToken(): string
    {

    }
}
