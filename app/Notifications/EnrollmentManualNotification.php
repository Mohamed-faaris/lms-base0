<?php

namespace App\Notifications;

use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class EnrollmentManualNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $courseId,
        public string $title,
        public string $message,
        public bool $sendEmail = true,
        public bool $sendPush = true
    ) {}

    public function via(object $notifiable): array
    {
        $channels = [\App\Notifications\Channels\AppNotificationChannel::class];

        if ($this->sendEmail) {
            $channels[] = \App\Notifications\Channels\SafeMailChannel::class;
        }

        if ($this->sendPush) {
            $channels[] = FcmChannel::class;
        }

        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => Route::has('admin.courses.show') ? route('admin.courses.show', $this->courseId) : null,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->greeting("Hi {$notifiable->name},")
            ->line($this->message)
            ->action('View Course', route('admin.courses.show', $this->courseId));
    }

    public function toFcm(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->message,
            'data' => [
                'type' => 'enrollment-manual',
                'course_id' => (string) $this->courseId,
            ],
        ];
    }
}
