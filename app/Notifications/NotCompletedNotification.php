<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class NotCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $courseId,
        public string $courseTitle,
        public string $batchKey,
        public int $daysUntilDeadline,
        public int $progress
    ) {}

    public function via(object $notifiable): array
    {
        return [\App\Notifications\Channels\AppNotificationChannel::class, \App\Notifications\Channels\SafeMailChannel::class, \App\Notifications\Channels\FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Course Ending Soon - Not Completed',
            'message' => "'{$this->courseTitle}' ends in {$this->daysUntilDeadline} days. You're at {$this->progress}% - finish strong!",
            'url' => Route::has('admin.courses.show') ? route('admin.courses.show', $this->courseId) : null,
        ];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Complete your course: {$this->courseTitle}")
            ->greeting("Hi {$notifiable->name},")
            ->line("'{$this->courseTitle}' ends in {$this->daysUntilDeadline} days.")
            ->line("You're at {$this->progress}% - almost there!")
            ->action('Continue Learning', route('admin.courses.show', $this->courseId))
            ->line('Finish strong!');
    }

    public function toFcm(object $notifiable): ?array
    {
        return [
            'title' => 'Course Ending Soon',
            'body' => "'{$this->courseTitle}' ends in {$this->daysUntilDeadline} days. You're at {$this->progress}%",
            'data' => [
                'type' => 'enrollment',
                'batch_key' => $this->batchKey,
            ],
        ];
    }
}
