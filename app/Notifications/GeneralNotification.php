<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;

    public function __construct($title, $message)
    {
        $this->title   = $title;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database']; // you can add 'mail', 'broadcast', 'fcm' later
    }

    public function toDatabase($notifiable)
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
        ];
    }
}
