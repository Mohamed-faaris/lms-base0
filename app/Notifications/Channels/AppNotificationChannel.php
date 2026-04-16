<?php

namespace App\Notifications\Channels;

use App\Enums\NotificationStatus;
use App\Models\Notification as AppNotification;
use Illuminate\Notifications\Notification;

class AppNotificationChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toDatabase')) {
            return;
        }

        $payload = call_user_func([$notification, 'toDatabase'], $notifiable);

        AppNotification::create([
            'user_id' => $notifiable->id,
            'subject' => $payload['subject'] ?? $payload['title'] ?? class_basename($notification),
            'description' => $payload['description'] ?? $payload['message'] ?? $payload['body'] ?? null,
            'status' => NotificationStatus::Active,
        ]);
    }
}
