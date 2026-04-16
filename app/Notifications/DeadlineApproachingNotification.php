<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeadlineApproachingNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $courseTitle,
        public string $batchKey,
        public int $daysUntilDeadline
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', \App\Notifications\Channels\FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Deadline Approaching',
            'message' => "'{$this->courseTitle}' deadline is in {$this->daysUntilDeadline} days. Complete it before it's too late!",
            'url' => route('faculty.courses.show', $this->batchKey),
        ];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Deadline Alert: {$this->courseTitle}")
            ->greeting("Hi {$notifiable->name},")
            ->line("'{$this->courseTitle}' deadline is in {$this->daysUntilDeadline} days.")
            ->action('Continue Learning', route('faculty.courses.show', $this->batchKey))
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
