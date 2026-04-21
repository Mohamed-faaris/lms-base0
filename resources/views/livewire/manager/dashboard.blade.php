<div class="space-y-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <flux:heading level="1" size="xl">Manager Dashboard</flux:heading>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Monitor the faculty accounts assigned to your college-wide and department-level scope.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <flux:button href="{{ route('manager.faculty.index') }}" wire:navigate>View Faculty Scope</flux:button>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Assigned Scopes</p>
            <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['assignedScopes'] }}</p>
        </div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Faculty In Scope</p>
            <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['facultyUsers'] }}</p>
        </div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Active Enrollments</p>
            <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['activeEnrollments'] }}</p>
        </div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Completions, 7 Days</p>
            <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['recentCompletions'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
        <section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Assigned Scope</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">College-wide scopes include every department in that college.</p>
                </div>
                <flux:badge color="blue">{{ count($scopeLabels) }}</flux:badge>
            </div>

            <div class="mt-5 flex flex-wrap gap-2">
                @forelse ($scopeLabels as $scopeLabel)
                    <flux:badge color="zinc">{{ $scopeLabel }}</flux:badge>
                @empty
                    <div class="rounded-2xl border border-dashed border-zinc-300 p-4 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                        No scopes assigned yet. Contact an admin to configure your faculty visibility.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Recent Faculty Accounts</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Newest faculty profiles visible inside your assigned scope.</p>
                </div>
                <flux:button variant="ghost" size="sm" href="{{ route('manager.faculty.index') }}" wire:navigate>
                    All Faculty
                </flux:button>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($recentFaculty as $faculty)
                    <a
                        href="{{ $faculty['profileUrl'] }}"
                        wire:navigate
                        class="block rounded-2xl border border-zinc-200 p-4 transition hover:border-zinc-300 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:border-zinc-600 dark:hover:bg-zinc-900/50"
                    >
                        <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $faculty['name'] }}</p>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $faculty['email'] }}</p>
                        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">{{ $faculty['college'] }} • {{ $faculty['department'] }}</p>
                    </a>
                @empty
                    <div class="rounded-2xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                        No faculty users match your current scopes yet.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
