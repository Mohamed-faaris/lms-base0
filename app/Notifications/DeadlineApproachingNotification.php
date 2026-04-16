<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class DeadlineApproachingNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $courseId,
        public string $courseTitle,
        public string $batchKey,
        public int $daysUntilDeadline
    ) {}

    public function via(object $notifiable): array
    {
        return [\App\Notifications\Channels\AppNotificationChannel::class, \App\Notifications\Channels\SafeMailChannel::class, \App\Notifications\Channels\FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Deadline Approaching',
            'message' => "'{$this->courseTitle}' deadline is in {$this->daysUntilDeadline} days. Complete it before it's too late!",
            'url' => Route::has('admin.courses.show') ? route('admin.courses.show', $this->courseId) : null,
        ];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Deadline Alert: {$this->courseTitle}")
            ->greeting("Hi {$notifiable->name},")
            ->line("'{$this->courseTitle}' deadline is in {$this->daysUntilDeadline} days.")
            ->action('Continue Learning', route('admin.courses.show', $this->courseId))
            ->line("Don't miss your deadline!");
    }

    public function toFcm(object $notifiable): ?array
    {
        return [
            'title' => 'Deadline Approaching',
            'body' => "'{$this->courseTitle}' deadline is in {$this->daysUntilDeadline} days!",
            'data' => [
                'type' => 'enrollment',
                'batch_key' => $this->batchKey,
            ],
        ];
    }
}
