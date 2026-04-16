<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NotCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $courseTitle,
        public string $batchKey,
        public int $daysUntilDeadline,
        public int $progress
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', \App\Notifications\Channels\FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Course Ending Soon - Not Completed',
            'message' => "'{$this->courseTitle}' ends in {$this->daysUntilDeadline} days. You're at {$this->progress}% - finish strong!",
            'url' => route('faculty.courses.show', $this->batchKey),
        ];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Complete your course: {$this->courseTitle}")
            ->greeting("Hi {$notifiable->name},")
            ->line("'{$this->courseTitle}' ends in {$this->daysUntilDeadline} days.")
            ->line("You're at {$this->progress}% - almost there!")
            ->action('Continue Learning', route('faculty.courses.show', $this->batchKey))
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
