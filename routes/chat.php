<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Chat\SendMessageController;
use App\Http\Controllers\API\Chat\GetConversationController;
use App\Http\Controllers\API\Chat\GetMessageController;


Route::middleware('auth:sanctum')->group(function () {
    // One-to-One Chat
    Route::post('/message/send', SendMessageController::class)->name('chat.send');
    Route::get('/conversations', GetConversationController::class)->name('chat.conversations');
    Route::get('/chat/messages', GetMessageController::class)->name('chat.messages');
    // Route::delete('/chat/message/{id}/delete', DeleteMessageController::class)->name('chat.delete');

    // Group Chat

    // (Optional) Add reactions
    // Route::post('/message/react/{id}', MessageReactController::class)->name('chat.react');
});
