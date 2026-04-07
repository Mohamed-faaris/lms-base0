<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading level="1" size="xl">Enrollments</flux:heading>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Review who is enrolled, which course they are in, and which deadlines need attention.</p>
        </div>
    </div>

    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Enrollments</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $totalEnrollments }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Active Learners</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $activeLearners }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Courses With Enrollments</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $activeCourses }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Urgent Deadlines</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $urgentDeadlines }}</p>
        </div>
    </div>

    <div class="mb-6">
        <flux:input
            wire:model.live="search"
            icon="magnifying-glass"
            placeholder="Search by learner, email, or course..."
        />
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
        <table class="w-full">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Learner</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Course</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Enrolled By</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Enrolled On</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Deadline</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($enrollments as $enrollment)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/40">
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $enrollment->learner }}</div>
                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $enrollment->learnerEmail }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-zinc-900 dark:text-zinc-100">
                            @if ($enrollment->courseUrl)
                                <a href="{{ $enrollment->courseUrl }}" class="font-medium hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $enrollment->course }}
                                </a>
                            @else
                                {{ $enrollment->course }}
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-zinc-600 dark:text-zinc-300">{{ $enrollment->enrolledBy }}</td>
                        <td class="px-4 py-4 text-sm text-zinc-500 dark:text-zinc-400">{{ $enrollment->enrolledAt }}</td>
                        <td class="px-4 py-4 text-sm font-medium {{ $enrollment->deadlineTone }}">{{ $enrollment->deadlineLabel }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                            No enrollments found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $enrollments->links() }}
    </div>
</div>
