<?php

namespace App\Livewire\Faculty;

use App\Enums\NotificationStatus;
use App\Models\Notification as AppNotification;
use Livewire\Component;

class Notifications extends Component
{
    public string $filter = 'unread';

    public function markViewed(int $notificationId): void
    {
        $notification = AppNotification::query()
            ->where('user_id', auth()->id())
            ->findOrFail($notificationId);

        $notification->update(['status' => NotificationStatus::Viewed]);
    }

    public function deleteNotification(int $notificationId): void
    {
        $notification = AppNotification::query()
            ->where('user_id', auth()->id())
            ->findOrFail($notificationId);

        $notification->update(['status' => NotificationStatus::Deleted]);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $notifications = AppNotification::query()
            ->with('user')
            ->where('user_id', auth()->id())
            ->when($this->filter === 'unread', fn ($query) => $query->where('status', NotificationStatus::Active))
            ->when($this->filter === 'read', fn ($query) => $query->where('status', NotificationStatus::Viewed))
            ->when($this->filter === 'deleted', fn ($query) => $query->where('status', NotificationStatus::Deleted))
            ->latest()
            ->get();

        return view('livewire.faculty.notifications', [
            'notifications' => $notifications,
            'unreadCount' => AppNotification::query()
                ->where('user_id', auth()->id())
                ->where('status', NotificationStatus::Active)
                ->count(),
        ])->layout('layouts.app');
    }
}
