<div class="space-y-6">
    <div class="flex items-center gap-4">
        <flux:button variant="ghost" href="{{ route('manager.faculty.index') }}" wire:navigate icon="arrow-left" />
        <div>
            <flux:heading level="1" size="xl">Faculty Profile</flux:heading>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Read-only manager view for {{ $user->name }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-start gap-4">
                <flux:avatar :name="$user->name" :initials="$user->initials()" class="h-16 w-16" />
                <div class="min-w-0 flex-1">
                    <flux:heading level="2" size="lg">{{ $user->name }}</flux:heading>
                    <p class="mt-1 break-all text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <flux:badge>{{ $user->role?->label() ?? 'N/A' }}</flux:badge>
                        <flux:badge color="zinc">{{ $user->college?->label() ?? 'N/A' }}</flux:badge>
                        <flux:badge color="zinc">{{ $user->department?->label() ?? 'N/A' }}</flux:badge>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-900/50">
                    <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Name</p>
                    <p class="mt-1 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</p>
                </div>
                <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-900/50">
                    <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Email</p>
                    <p class="mt-1 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $user->email }}</p>
                </div>
                <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-900/50">
                    <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">College</p>
                    <p class="mt-1 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $user->college?->label() ?? 'N/A' }}</p>
                </div>
                <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-900/50">
                    <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Department</p>
                    <p class="mt-1 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $user->department?->label() ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Account Summary</p>
            <div class="mt-4 space-y-3 text-sm text-zinc-600 dark:text-zinc-300">
                <div class="flex items-center justify-between gap-4">
                    <span>Role</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user->role?->label() ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <span>Enrollments</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ count($enrollments) }}</span>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <span>Joined</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user->created_at?->format('M d, Y') ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
        <div>
            <flux:heading level="2" size="lg">Enrollments</flux:heading>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Read-only progress and deadline summary.</p>
        </div>

        <div class="mt-4 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Course</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Progress</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Deadline</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Enrolled</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($enrollments as $enrollment)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/40">
                            <td class="px-4 py-4 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $enrollment->course }}</td>
                            <td class="px-4 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                <div class="flex items-center gap-3">
                                    <div class="h-2 w-36 rounded-full bg-zinc-200 dark:bg-zinc-700">
                                        <div class="h-2 rounded-full bg-blue-500" style="width: {{ $enrollment->progress }}%"></div>
                                    </div>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $enrollment->progress }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm font-medium {{ $enrollment->deadlineTone }}">{{ $enrollment->deadlineLabel }}</td>
                            <td class="px-4 py-4 text-sm text-zinc-600 dark:text-zinc-300">{{ $enrollment->enrolledAt }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                No enrollments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
