<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use SerializesModels;

    public $message;

    public function __construct($message)
    {

        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat-channel.' . $this->message->receiver_id);
    }
}
