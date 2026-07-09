<?php

namespace App\Listeners;

use App\Services\DingTalkRobotNotifierService;
use Illuminate\Log\Events\MessageLogged;
use Throwable;

class SendDingTalkExceptionAlertListener
{
    /**
     * @var DingTalkRobotNotifierService
     */
    protected $notifier;

    public function __construct(DingTalkRobotNotifierService $notifier)
    {
        $this->notifier = $notifier;
    }

    public function handle(MessageLogged $event)
    {
        if (!$this->shouldSend($event)) {
            return;
        }

        try {
            $this->notifier->notify($event);
        } catch (Throwable $exception) {
            error_log('[DingTalk exception alert failed] ' . $exception->getMessage());
        }
    }

    protected function shouldSend(MessageLogged $event): bool
    {
        $levels = config('services.dingtalk_exception.levels', [
            'error',
            'critical',
            'alert',
            'emergency',
        ]);
        $levels = array_map('trim', $levels);

        if (!in_array($event->level, $levels)) {
            return false;
        }

        if ((bool) config('services.dingtalk_exception.exception_only', true)) {
            return isset($event->context['exception'])
                && $event->context['exception'] instanceof Throwable;
        }

        return true;
    }
}
