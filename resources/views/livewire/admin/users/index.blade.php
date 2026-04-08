<div>
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <flux:heading level="1" size="xl">Users</flux:heading>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Browse every account and open a profile to review or update details.
            </p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-950/30">
            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
        </div>
    @endif

    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Users</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $totalUsers }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Admin Users</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $adminUsers }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Faculty Users</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $facultyUsers }}</p>
        </div>
    </div>

    <div class="mb-6">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Search by name or email..." />
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
        <table class="w-full">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">User</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">College</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Department</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Role</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($users as $user)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/40">
                        <td class="px-4 py-4">
                            <a href="{{ route('admin.users.profile', $user) }}" wire:navigate class="flex items-center gap-3 group">
                                <flux:avatar :name="$user->name" :initials="$user->initials()" class="h-10 w-10" />
                                <div>
                                    <div class="font-medium text-zinc-900 group-hover:text-blue-600 dark:text-zinc-100 dark:group-hover:text-blue-400">
                                        {{ $user->name }}
                                    </div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</div>
                                </div>
                            </a>
                        </td>
                        <td class="px-4 py-4 text-sm text-zinc-600 dark:text-zinc-300">{{ $user->college?->label() ?? 'N/A' }}</td>
                        <td class="px-4 py-4 text-sm text-zinc-600 dark:text-zinc-300">{{ $user->department?->label() ?? 'N/A' }}</td>
                        <td class="px-4 py-4 text-sm text-zinc-600 dark:text-zinc-300">{{ $user->role?->label() ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
