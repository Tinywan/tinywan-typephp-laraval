<?php

namespace App\Providers;

use App\Events\UserCreatedEvent;
use App\Listeners\JuBaoZanUserCreatedListener;
use App\Listeners\SendUserNotificationListener;
use App\Listeners\UserEventSubscriber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserCreatedEvent::class => [
            SendUserNotificationListener::class,
            JuBaoZanUserCreatedListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }

    /** 要注册的订阅者类。@var array */
    protected $subscribe = [
        UserEventSubscriber::class
    ];
}
