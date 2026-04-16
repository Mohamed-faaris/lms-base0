<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class NotStartedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $courseId,
        public string $courseTitle,
        public string $batchKey,
        public int $daysSinceEnrolled
    ) {}

    public function via(object $notifiable): array
    {
        return [\App\Notifications\Channels\AppNotificationChannel::class, \App\Notifications\Channels\SafeMailChannel::class, \App\Notifications\Channels\FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Course Not Started',
            'message' => "You haven't started '{$this->courseTitle}' yet. It's been {$this->daysSinceEnrolled} days since you were enrolled.",
            'url' => Route::has('admin.courses.show') ? route('admin.courses.show', $this->courseId) : null,
        ];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Reminder: Start your course - {$this->courseTitle}")
            ->greeting("Hi {$notifiable->name},")
            ->line("You haven't started '{$this->courseTitle}' yet. It's been {$this->daysSinceEnrolled} days since you were enrolled.")
            ->action('Start Course', route('admin.courses.show', $this->courseId))
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
