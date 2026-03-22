<div>
    <flux:heading level="1" size="xl">Welcome, Admin</flux:heading>
    <p class="text-zinc-500 dark:text-zinc-400 mt-1">Manage your courses and users from this dashboard.</p>

    <div class="grid gap-4 mt-6 md:grid-cols-3">
        {{-- Total Courses --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-indigo-500/20 flex items-center justify-center">
                    <flux:icon.academic-cap class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                        {{ \App\Models\Course::count() }}
                    </p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Courses</p>
                </div>
            </div>
        </div>

        {{-- Total Enrollments --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-green-500/20 flex items-center justify-center">
                    <flux:icon.user-group class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                        {{ \App\Models\Enrollment::count() }}
                    </p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Enrollments</p>
                </div>
            </div>
        </div>

        {{-- Total Users --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-blue-500/20 flex items-center justify-center">
                    <flux:icon.users class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                        {{ \App\Models\User::count() }}
                    </p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Users</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8">
        <flux:heading level="2" size="lg">Quick Actions</flux:heading>
        <div class="flex gap-4 mt-4">
            <flux:button href="{{ route('admin.courses.create') }}" wire:navigate>
                <flux:icon.plus variant="mini" class="mr-2" />
                Create Course
            </flux:button>
        </div>
    </div>
</div>