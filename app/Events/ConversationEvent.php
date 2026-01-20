<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $senderId;
    public $receiverId;
    public $conversation;


    public function __construct($senderId, $receiverId = null, $conversation)
    {
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->conversation = $conversation;
    }


    public function broadcastOn(): array
    {

        return [
            new PrivateChannel('conversation-channel.' . $this->receiverId),
        ];
    }

    /**
     * Function : broadcastWith
     * @return array
     * */
    public function broadcastWith(): array
    {
        return [
            'senderId'   => $this->senderId,
            'receiverId' => $this->receiverId,
            'conversation' => $this->conversation
        ];
    }

    // public function broadcastAs(): string
    // {
    //     return 'conversation.updated';
    // }
}
