<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat-channel.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
Broadcast::channel('conversation-channel.{receiverId}', function ($user, $receiverId) {
    return (int) $user->id === (int) $receiverId;
});
