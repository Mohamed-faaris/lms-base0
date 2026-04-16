<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class DeadlineReachedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $courseId,
        public string $courseTitle,
        public string $batchKey
    ) {}

    public function via(object $notifiable): array
    {
        return [\App\Notifications\Channels\AppNotificationChannel::class, \App\Notifications\Channels\SafeMailChannel::class, \App\Notifications\Channels\FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Deadline Reached',
            'message' => "The deadline for '{$this->courseTitle}' has passed. Contact your administrator if you need an extension.",
            'url' => Route::has('admin.courses.show') ? route('admin.courses.show', $this->courseId) : null,
        ];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Deadline Passed: {$this->courseTitle}")
            ->greeting("Hi {$notifiable->name},")
            ->line("The deadline for '{$this->courseTitle}' has passed.")
            ->line('Contact your administrator if you need an extension.')
            ->action('View Course', route('admin.courses.show', $this->courseId));
    }

    public function toFcm(object $notifiable): ?array
    {
        return [
            'title' => 'Deadline Reached',
            'body' => "The deadline for '{$this->courseTitle}' has passed.",
            'data' => [
                'type' => 'enrollment',
                'batch_key' => $this->batchKey,
            ],
        ];
    }
}
