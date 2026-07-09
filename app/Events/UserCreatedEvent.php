<?php

namespace App\Events;

use App\Models\Shop;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var Shop
     */
    public $shop;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Shop $shop)
    {
        $this->user = $user;
        $this->shop = $shop;
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
