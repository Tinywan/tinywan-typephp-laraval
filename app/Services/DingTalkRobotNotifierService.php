<?php

namespace App\Services;

use Illuminate\Log\Events\MessageLogged;
use RuntimeException;
use Throwable;

class DingTalkRobotNotifierService
{
    public function notify(MessageLogged $event)
    {
        if (!$this->enabled()) {
            return;
        }

        $alert = $this->alertContext($event);

        $this->postJson($this->webhookUrl($alert['is_prod']), $this->payload($alert));
    }

    public function enabled()
    {
        return (bool) config('services.dingtalk_exception.enabled')
            && (bool) config('services.dingtalk_exception.webhook');
    }

    protected function webhookUrl($isProd = false)
    {
        $webhook = $isProd
            ? config('services.dingtalk_exception.prod_webhook', config('services.dingtalk_exception.webhook'))
            : config('services.dingtalk_exception.webhook');
        $secret = $isProd
            ? config('services.dingtalk_exception.prod_secret', config('services.dingtalk_exception.secret'))
            : config('services.dingtalk_exception.secret');

        if (!$webhook) {
            $webhook = config('services.dingtalk_exception.webhook');
        }

        if (!$secret) {
            $secret = config('services.dingtalk_exception.secret');
        }

        if (!$secret) {
            return $webhook;
        }

        $timestamp = (string) floor(microtime(true) * 1000);
        $stringToSign = $timestamp . "\n" . $secret;
        $sign = urlencode(base64_encode(hash_hmac('sha256', $stringToSign, $secret, true)));
        $separator = strpos($webhook, '?') === false ? '?' : '&';

        return $webhook . $separator . 'timestamp=' . $timestamp . '&sign=' . $sign;
    }

    protected function payload(array $alert)
    {
        $mobiles = config('services.dingtalk_exception.at_mobiles', []);
        $atText = '';

        if (!empty($mobiles)) {
            $atText = "\n\n@" . implode(' @', $mobiles);
        }

        return [
            'msgtype' => 'markdown',
            'markdown' => [
                'title' => $alert['title'],
                'text' => $alert['message'] . $atText,
            ],
            'at' => [
                'atMobiles' => $mobiles,
                'isAtAll' => (bool) config('services.dingtalk_exception.at_all', false),
            ],
        ];
    }

    protected function postJson($url, array $payload)
    {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $timeout = (float) config('services.dingtalk_exception.timeout', 3);

        if (function_exists('curl_init')) {
            $ch = curl_init($url);

            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS => $json,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => $timeout,
                CURLOPT_TIMEOUT => $timeout,
            ]);

            $response = curl_exec($ch);
            $error = curl_error($ch);
            $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if ($response === false) {
                throw new RuntimeException($error ?: 'DingTalk request failed.');
            }

            if ($status >= 400) {
                throw new RuntimeException('DingTalk request failed with HTTP status ' . $status);
            }

            $this->assertDingTalkResponse($response);

            return;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => $json,
                'timeout' => $timeout,
            ],
        ]);

        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            throw new RuntimeException('DingTalk request failed.');
        }

        $this->assertDingTalkResponse($response);
    }

    protected function assertDingTalkResponse($response)
    {
        $data = json_decode($response, true);

        if (!is_array($data) || !array_key_exists('errcode', $data)) {
            return;
        }

        if ((int) $data['errcode'] !== 0) {
            throw new RuntimeException('DingTalk request failed: ' . $response);
        }
    }

    protected function alertContext(MessageLogged $event)
    {
        $exception = $this->exceptionFrom($event);
        $request = app()->runningInConsole() ? null : (app()->bound('request') ? request() : null);

        $args = [
            'origin' => $this->origin($request),
            'domain' => $this->domain($request),
            'sub_domain' => $this->subDomain($request),
            'method' => $request ? $request->method() : 'CONSOLE',
            'url' => $request ? $request->path() : 'redis-queue',
            'message' => $this->inline($event->message),
            'ip' => $request ? $request->ip() : '--',
            'timestamp' => date('Y-m-d H:i:s'),
            'file' => $this->exceptionFile($exception, $event->context),
            'line' => $this->exceptionLine($exception, $event->context),
            'param' => $this->params($request, $event->context),
            'trace_id' => $this->traceId($request, $event->context),
        ];

        return $this->markdownFromArgs($args);
    }

    protected function markdownFromArgs(array $args)
    {
        $env = '开发环境';
        $color = '#5a5e66';
        $isProd = false;

        if (app()->runningInConsole()) {
            $isProd = config('app.env') === 'production';
            $env = $isProd ? '正式环境（命令行）' : '测试环境（命令行）';
            $color = $isProd ? '#e53935' : '#e53935';

            if (($args['url'] ?? '') === 'redis-queue') {
                $args['url'] = 'Message Queue';
            }
        } else {
            if (($args['sub_domain'] ?? '') === 'api') {
                $env = '正式环境';
                $color = '#e53935';
                $isProd = true;
            } elseif (($args['sub_domain'] ?? '') === 'pre-api') {
                $env = '预发布环境';
                $color = '#e6a23c';
            } elseif (($args['sub_domain'] ?? '') === 'gray-api') {
                $env = '灰度环境';
                $color = '#f78989';
            } elseif (($args['sub_domain'] ?? '') === 'api-test') {
                $env = '测试环境';
                $color = '#85ce61';
            }
        }

        $project = config('services.dingtalk_exception.project', config('app.name'));
        $message = '### ' . config('services.dingtalk_exception.keyword', 'Laravel异常报警') . " \n";
        $message .= ' - <font color="' . $color . '">监控来源： ' . $env . ' </font> ' . " \n";
        $message .= ' - 请求站点：' . ($args['origin'] ?? '--') . " \n";
        $message .= ' - 请求域名：' . ($args['domain'] ?? '--') . " \n";
        $message .= ' - 请求方法：' . ($args['method'] ?? '') . " \n";
        $message .= ' - 请求路由：' . ($args['url'] ?? '--') . " \n";
        $message .= ' - 响应错误： ' . ($args['message'] ?? '--') . " \n";
        $message .= ' - 请求IP：' . ($args['ip'] ?? '--') . " \n";
        $message .= ' - 请求时间：' . ($args['timestamp'] ?? date('Y-m-d H:i:s')) . " \n";
        $message .= ' - 异常文件：' . ($args['file'] ?? '--') . " \n";
        $message .= ' - 文件行数：' . ($args['line'] ?? '--') . " \n";
        $message .= ' - 请求参数：' . $this->limit(json_encode($args['param'] ?? [], JSON_UNESCAPED_UNICODE), 1800) . " \n";
        $message .= ' - TraceId：' . ($args['trace_id'] ?? '--') . " \n";

        return [
            'title' => '[报警] <' . $project . '-' . $env . '>',
            'message' => $message,
            'is_prod' => $isProd,
        ];
    }

    protected function exceptionFrom(MessageLogged $event)
    {
        return $event->context['exception'] ?? null;
    }

    protected function contextWithoutException(array $context)
    {
        unset($context['exception']);

        return $context;
    }

    protected function origin($request)
    {
        if (!$request) {
            return config('app.url', '--');
        }

        return $request->headers->get('origin') ?: config('app.url', '--');
    }

    protected function domain($request)
    {
        if (!$request) {
            return parse_url(config('app.url'), PHP_URL_HOST) ?: '--';
        }

        return $request->getHost();
    }

    protected function subDomain($request)
    {
        $domain = $this->domain($request);

        if ($domain === '--') {
            return '';
        }

        $parts = explode('.', $domain);

        return count($parts) > 2 ? $parts[0] : '';
    }

    protected function exceptionFile($exception, array $context)
    {
        if ($exception instanceof Throwable) {
            return $exception->getFile();
        }

        return $context['file'] ?? '--';
    }

    protected function exceptionLine($exception, array $context)
    {
        if ($exception instanceof Throwable) {
            return $exception->getLine();
        }

        return $context['line'] ?? '--';
    }

    protected function params($request, array $context)
    {
        if ($request) {
            return $this->maskSensitive($request->all());
        }

        return $this->maskSensitive($this->contextWithoutException($context));
    }

    protected function traceId($request, array $context)
    {
        if ($request && isset($request->traceId)) {
            return $request->traceId;
        }

        if ($request) {
            return $request->headers->get('X-Trace-Id')
                ?: $request->headers->get('X-Request-Id')
                ?: ($context['trace_id'] ?? '--');
        }

        return $context['trace_id'] ?? '--';
    }

    protected function maskSensitive(array $data)
    {
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'token',
            'access_token',
            'authorization',
        ];

        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), $sensitiveKeys)) {
                $data[$key] = '***';
                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->maskSensitive($value);
            }
        }

        return $data;
    }

    protected function currentUserId()
    {
        try {
            return auth()->id();
        } catch (Throwable $exception) {
            return null;
        }
    }

    protected function inline($value)
    {
        return str_replace(["\r", "\n"], ' ', (string) $value);
    }

    protected function limit($value, $limit)
    {
        $value = (string) $value;

        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return mb_substr($value, 0, $limit) . '...';
    }
}
