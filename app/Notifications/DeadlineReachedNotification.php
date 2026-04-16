<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeadlineReachedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $courseTitle,
        public string $batchKey
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', \App\Notifications\Channels\FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Deadline Reached',
            'message' => "The deadline for '{$this->courseTitle}' has passed. Contact your administrator if you need an extension.",
            'url' => route('faculty.courses.show', $this->batchKey),
        ];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Deadline Passed: {$this->courseTitle}")
            ->greeting("Hi {$notifiable->name},")
            ->line("The deadline for '{$this->courseTitle}' has passed.")
            ->line('Contact your administrator if you need an extension.')
            ->action('View Course', route('faculty.courses.show', $this->batchKey));
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
