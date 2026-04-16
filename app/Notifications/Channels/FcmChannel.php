<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;

class FcmChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toFcm')) {
            return;
        }

        $message = $notification->toFcm($notifiable);

        if ($message === null) {
            return;
        }

        $token = $notifiable->fcm_token ?? null;

        if (! $token) {
            return;
        }

        try {
            $messaging = app('firebase.messaging');
            $messaging->send([
                'token' => $token,
                'notification' => [
                    'title' => $message['title'],
                    'body' => $message['body'],
                ],
                'data' => $message['data'] ?? [],
            ]);
        } catch (\Exception $e) {
            report($e);
        }
    }
}
