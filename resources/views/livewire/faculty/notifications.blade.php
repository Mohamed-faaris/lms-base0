<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-950 dark:text-white">Notifications</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $unreadCount }} unread notification{{ $unreadCount === 1 ? '' : 's' }}</p>
        </div>

        <div class="flex gap-2">
            <flux:button variant="ghost" wire:click="$set('filter', 'unread')">Unread</flux:button>
            <flux:button variant="ghost" wire:click="$set('filter', 'read')">Read</flux:button>
            <flux:button variant="ghost" wire:click="$set('filter', 'deleted')">Deleted</flux:button>
        </div>
    </div>

    <div class="space-y-3">
        @forelse ($notifications as $notification)
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <h2 class="font-medium text-zinc-950 dark:text-white">{{ $notification->subject }}</h2>
                            <flux:badge size="sm" color="{{ $notification->status->value === 'active' ? 'blue' : ($notification->status->value === 'viewed' ? 'emerald' : 'zinc') }}">
                                {{ $notification->status->label() }}
                            </flux:badge>
                        </div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-300">{{ $notification->description }}</p>
                    </div>

                    <div class="flex gap-2">
                        @if ($notification->status->value === 'active')
                            <flux:button size="sm" variant="outline" wire:click="markViewed({{ $notification->id }})">Mark viewed</flux:button>
                        @endif
                        @if ($notification->status->value !== 'deleted')
                            <flux:button size="sm" variant="ghost" wire:click="deleteNotification({{ $notification->id }})">Delete</flux:button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50 p-8 text-center dark:border-zinc-700 dark:bg-zinc-900/40">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">No notifications found.</p>
            </div>
        @endforelse
    </div>
</div>
