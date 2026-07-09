<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'dingtalk_exception' => [
        'enabled' => env('DINGTALK_EXCEPTION_ENABLED', false),
        'webhook' => env('DINGTALK_EXCEPTION_WEBHOOK'),
        'prod_webhook' => env('DINGTALK_EXCEPTION_PROD_WEBHOOK'),
        'secret' => env('DINGTALK_EXCEPTION_SECRET'),
        'prod_secret' => env('DINGTALK_EXCEPTION_PROD_SECRET'),
        'keyword' => env('DINGTALK_EXCEPTION_KEYWORD', 'Laravel异常报警'),
        'project' => env('DINGTALK_EXCEPTION_PROJECT', env('DINGTALK_EXCEPTION_KEYWORD', env('APP_NAME', 'Laravel'))),
        'levels' => array_filter(explode(',', env('DINGTALK_EXCEPTION_LEVELS', 'error,critical,alert,emergency'))),
        'exception_only' => env('DINGTALK_EXCEPTION_ONLY', false),
        'at_mobiles' => array_filter(explode(',', env('DINGTALK_EXCEPTION_AT_MOBILES', ''))),
        'at_all' => env('DINGTALK_EXCEPTION_AT_ALL', false),
        'timeout' => env('DINGTALK_EXCEPTION_TIMEOUT', 3),
    ],

    'jubaozan' => [
        'host' => env('JUBAOZAN_HOST', 'https://api.c3tech.cn'),
        'client_id' => env('JUBAOZAN_CLIENT_ID', ''),
        'client_secret' => env('JUBAOZAN_CLIENT_SECRET', ''),
        'redirect_uri' => env('JUBAOZAN_REDIRECT_URI', 'https://shop.chutang66.com/jubaozan/authBack'),
        'white_shop_ids' => explode(',', env('JUBAOZAN_WHITE_SHOP_IDS', '1')),
        'webhook_url' => env('JUBAOZAN_WEBHOOK_URL', 'https://shop.chutang66.com/jubaozan/webhook'),
    ],

];
