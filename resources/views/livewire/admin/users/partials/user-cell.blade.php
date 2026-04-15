<a href="{{ route('admin.users.profile', $user->id) }}" wire:navigate class="flex items-center gap-3 group">
    <flux:avatar name="{{ $user->name }}" initials="{{ $initials }}" class="h-10 w-10" />
    <div>
        <div class="font-medium text-zinc-900 group-hover:text-blue-600 dark:text-zinc-100 dark:group-hover:text-blue-400">
            {{ $user->name }}
        </div>
        <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</div>
    </div>
</a>