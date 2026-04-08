<div class="space-y-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <flux:heading level="1" size="xl">Admin Dashboard</flux:heading>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Monitor learning activity, catch deadlines that need attention, and jump into the next admin action.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <flux:button href="{{ route('admin.courses.create') }}" wire:navigate>Create Course</flux:button>
            <flux:button variant="ghost" href="{{ route('admin.enrollments.create') }}" wire:navigate>Create Enrollment Batch</flux:button>
            <flux:button variant="ghost" href="{{ route('admin.users.index') }}" wire:navigate>View Users</flux:button>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Users</p>
                    <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['totalUsers'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400">
                    <flux:icon.users class="h-6 w-6" />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Faculty Users</p>
                    <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['facultyUsers'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-violet-500/10 text-violet-600 dark:text-violet-400">
                    <flux:icon.user-group class="h-6 w-6" />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Courses</p>
                    <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['totalCourses'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                    <flux:icon.academic-cap class="h-6 w-6" />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Enrollments</p>
                    <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['totalEnrollments'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <flux:icon.clipboard-document-list class="h-6 w-6" />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Enrollments, 7 Days</p>
                    <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['recentEnrollments'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400">
                    <flux:icon.arrow-trending-up class="h-6 w-6" />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Completions, 7 Days</p>
                    <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['recentCompletions'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-cyan-500/10 text-cyan-600 dark:text-cyan-400">
                    <flux:icon.check-badge class="h-6 w-6" />
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.3fr_1fr]">
        <div class="grid gap-6 md:grid-cols-2">
            <section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Enrollment Trend</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Daily volume over the last 7 days.</p>
                    </div>
                    <flux:badge color="amber" variant="subtle">{{ $stats['recentEnrollments'] }} total</flux:badge>
                </div>

                <div class="mt-6 flex h-44 items-end gap-3">
                    @foreach ($enrollmentTrend as $day)
                        <div class="flex flex-1 flex-col items-center gap-2" wire:key="enrollment-trend-{{ $day['label'] }}">
                            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $day['value'] }}</span>
                            <div class="flex h-32 w-full items-end rounded-2xl bg-zinc-100 p-2 dark:bg-zinc-900/70">
                                <div
                                    class="w-full rounded-xl bg-gradient-to-t from-amber-500 to-orange-400"
                                    style="height: {{ max(10, (int) round(($day['value'] / $day['max']) * 100)) }}%;"
                                ></div>
                            </div>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Completion Trend</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Finished lessons recorded in the last 7 days.</p>
                    </div>
                    <flux:badge color="cyan" variant="subtle">{{ $stats['recentCompletions'] }} total</flux:badge>
                </div>

                <div class="mt-6 flex h-44 items-end gap-3">
                    @foreach ($completionTrend as $day)
                        <div class="flex flex-1 flex-col items-center gap-2" wire:key="completion-trend-{{ $day['label'] }}">
                            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $day['value'] }}</span>
                            <div class="flex h-32 w-full items-end rounded-2xl bg-zinc-100 p-2 dark:bg-zinc-900/70">
                                <div
                                    class="w-full rounded-xl bg-gradient-to-t from-cyan-600 to-sky-400"
                                    style="height: {{ max(10, (int) round(($day['value'] / $day['max']) * 100)) }}%;"
                                ></div>
                            </div>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Urgent Deadlines</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Batches that need attention now.</p>
                </div>
                <flux:button variant="ghost" size="sm" href="{{ route('admin.enrollments.index') }}" wire:navigate>
                    View Enrollments
                </flux:button>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($urgentBatches as $batch)
                    <a
                        href="{{ $batch['enrollmentUrl'] }}"
                        wire:navigate
                        class="block rounded-2xl border border-zinc-200 p-4 transition hover:border-zinc-300 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:border-zinc-600 dark:hover:bg-zinc-900/50"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $batch['course'] }}</p>
                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                    Batch {{ $batch['batchId'] }} • {{ $batch['learnersCount'] }} learner{{ $batch['learnersCount'] === 1 ? '' : 's' }}
                                </p>
                                <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Assigned by {{ $batch['enrolledBy'] }} on {{ $batch['enrolledAt'] }}</p>
                            </div>
                            <flux:badge color="{{ $batch['isOverdue'] ? 'red' : 'amber' }}">
                                {{ $batch['deadlineCompactLabel'] }}
                            </flux:badge>
                        </div>
                    </a>
                @empty
                    <div class="rounded-2xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                        No overdue or near-term batches. Current deadlines look healthy.
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Recent Enrollment Batches</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Most recent course assignments across the platform.</p>
                </div>
                <flux:button variant="ghost" size="sm" href="{{ route('admin.enrollments.index') }}" wire:navigate>
                    All Batches
                </flux:button>
            </div>

            <div class="mt-5 overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-700">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Batch</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Course</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Users</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Deadline</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($recentBatches as $batch)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/40" wire:key="recent-batch-{{ $batch['id'] }}">
                                <td class="px-4 py-4">
                                    <a
                                        href="{{ $batch['enrollmentUrl'] }}"
                                        wire:navigate
                                        class="inline-flex rounded-md bg-zinc-100 px-2 py-1 font-mono text-xs text-zinc-700 transition hover:bg-zinc-200 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800"
                                    >
                                        {{ $batch['batchId'] }}
                                    </a>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($batch['courseUrl'])
                                        <a href="{{ $batch['courseUrl'] }}" wire:navigate class="text-sm font-medium text-zinc-900 hover:text-blue-600 dark:text-zinc-100 dark:hover:text-blue-400">
                                            {{ $batch['course'] }}
                                        </a>
                                    @else
                                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $batch['course'] }}</span>
                                    @endif
                                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $batch['enrolledBy'] }} • {{ $batch['enrolledAt'] }}</p>
                                </td>
                                <td class="px-4 py-4 text-sm text-zinc-600 dark:text-zinc-300">{{ $batch['learnersCount'] }}</td>
                                <td class="px-4 py-4">
                                    <span class="text-sm {{ $batch['isOverdue'] ? 'text-red-600 dark:text-red-400' : ($batch['isUrgent'] ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-600 dark:text-zinc-300') }}">
                                        {{ $batch['deadlineLabel'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    No enrollment batches yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="grid gap-6">
            <section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Top Courses by Enrollments</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Most assigned courses overall.</p>
                    </div>
                    <flux:button variant="ghost" size="sm" href="{{ route('admin.courses.index') }}" wire:navigate>Courses</flux:button>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($topEnrollmentCourses as $course)
                        <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700" wire:key="top-enrollment-course-{{ $course['title'] }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <a href="{{ $course['showUrl'] }}" wire:navigate class="text-sm font-semibold text-zinc-900 hover:text-blue-600 dark:text-zinc-100 dark:hover:text-blue-400">
                                        {{ $course['title'] }}
                                    </a>
                                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $course['enrollmentsCount'] }} enrollment{{ $course['enrollmentsCount'] === 1 ? '' : 's' }}</p>
                                </div>
                                <flux:button variant="ghost" size="sm" href="{{ $course['analyzeUrl'] }}" wire:navigate>Analyze</flux:button>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                            No courses have enrollments yet.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Top Courses by Completions</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Most finished lessons across all courses.</p>
                    </div>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($topCompletionCourses as $course)
                        <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700" wire:key="top-completion-course-{{ $course['title'] }}">
                            <a href="{{ $course['showUrl'] }}" wire:navigate class="text-sm font-semibold text-zinc-900 hover:text-blue-600 dark:text-zinc-100 dark:hover:text-blue-400">
                                {{ $course['title'] }}
                            </a>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $course['completionsCount'] }} completion{{ $course['completionsCount'] === 1 ? '' : 's' }}</p>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                            No completion data yet.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Courses With No Recent Progress</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Enrolled courses with zero completions in the last 14 days.</p>
                </div>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($staleCourses as $course)
                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700" wire:key="stale-course-{{ $course['title'] }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ $course['showUrl'] }}" wire:navigate class="text-sm font-semibold text-zinc-900 hover:text-blue-600 dark:text-zinc-100 dark:hover:text-blue-400">
                                    {{ $course['title'] }}
                                </a>
                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $course['enrollmentCount'] }} enrolled learner{{ $course['enrollmentCount'] === 1 ? '' : 's' }}</p>
                            </div>
                            <flux:button variant="ghost" size="sm" href="{{ $course['analyzeUrl'] }}" wire:navigate>Analyze</flux:button>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                        All enrolled courses have recent completion activity.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Recent Feedback</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Latest course reactions from learners.</p>
                </div>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($recentFeedback as $feedback)
                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700" wire:key="feedback-{{ $feedback['course'] }}-{{ $feedback['user'] }}-{{ $feedback['createdAt'] }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                @if ($feedback['courseUrl'])
                                    <a href="{{ $feedback['courseUrl'] }}" wire:navigate class="text-sm font-semibold text-zinc-900 hover:text-blue-600 dark:text-zinc-100 dark:hover:text-blue-400">
                                        {{ $feedback['course'] }}
                                    </a>
                                @else
                                    <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $feedback['course'] }}</p>
                                @endif
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $feedback['user'] }} • {{ $feedback['createdAt'] }}</p>
                                @if ($feedback['comment'])
                                    <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">{{ $feedback['comment'] }}</p>
                                @endif
                            </div>
                            @if ($feedback['rating'])
                                <flux:badge color="emerald" variant="subtle">{{ $feedback['rating'] }}/5</flux:badge>
                            @else
                                <flux:badge color="zinc" variant="subtle">No rating</flux:badge>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                        No learner feedback has been submitted yet.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
