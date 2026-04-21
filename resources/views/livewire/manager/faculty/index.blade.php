<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <flux:heading level="1" size="xl">Faculty Scope</flux:heading>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Read-only faculty list filtered by your assigned colleges and departments.
            </p>
        </div>
        <flux:button variant="ghost" href="{{ route('manager.dashboard') }}" wire:navigate>Back to Dashboard</flux:button>
    </div>

    <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Current Scope</p>
        <div class="mt-4 flex flex-wrap gap-2">
            @forelse ($scopeLabels as $scopeLabel)
                <flux:badge color="zinc">{{ $scopeLabel }}</flux:badge>
            @empty
                <div class="rounded-2xl border border-dashed border-zinc-300 p-4 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                    No scopes assigned yet. Contact an admin to grant visibility.
                </div>
            @endforelse
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
        <table class="w-full">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Faculty</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">College</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Department</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Enrollments</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($faculty as $member)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/40">
                        <td class="px-4 py-4">
                            <a href="{{ $member['profileUrl'] }}" wire:navigate class="text-sm font-medium text-zinc-900 hover:text-blue-600 dark:text-zinc-100 dark:hover:text-blue-400">
                                {{ $member['name'] }}
                            </a>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $member['email'] }}</p>
                        </td>
                        <td class="px-4 py-4 text-sm text-zinc-600 dark:text-zinc-300">{{ $member['college'] }}</td>
                        <td class="px-4 py-4 text-sm text-zinc-600 dark:text-zinc-300">{{ $member['department'] }}</td>
                        <td class="px-4 py-4 text-sm text-zinc-600 dark:text-zinc-300">{{ $member['enrollmentsCount'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                            No faculty users are available in your current scope.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
