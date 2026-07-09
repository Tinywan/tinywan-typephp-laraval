<?php

namespace App\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoginEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Authenticatable
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
