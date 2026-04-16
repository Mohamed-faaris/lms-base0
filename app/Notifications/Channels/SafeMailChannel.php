<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Channels\MailChannel;
use Illuminate\Notifications\Notification;
use Throwable;

class SafeMailChannel extends MailChannel
{
    public function send($notifiable, Notification $notification): void
    {
        try {
            parent::send($notifiable, $notification);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
