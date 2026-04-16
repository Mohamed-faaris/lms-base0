<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NotStartedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $courseTitle,
        public string $batchKey,
        public int $daysSinceEnrolled
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', \App\Notifications\Channels\FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Course Not Started',
            'message' => "You haven't started '{$this->courseTitle}' yet. It's been {$this->daysSinceEnrolled} days since you were enrolled.",
            'url' => route('faculty.courses.show', $this->batchKey),
        ];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Reminder: Start your course - {$this->courseTitle}")
            ->greeting("Hi {$notifiable->name},")
            ->line("You haven't started '{$this->courseTitle}' yet. It's been {$this->daysSinceEnrolled} days since you were enrolled.")
            ->action('Start Course', route('faculty.courses.show', $this->batchKey))
            ->line('Start learning today!');
    }

    public function toFcm(object $notifiable): ?array
    {
        return [
            'title' => 'Course Not Started',
            'body' => "You haven't started '{$this->courseTitle}' yet. It's been {$this->daysSinceEnrolled} days since you were enrolled.",
            'data' => [
                'type' => 'enrollment',
                'batch_key' => $this->batchKey,
            ],
        ];
    }
}
