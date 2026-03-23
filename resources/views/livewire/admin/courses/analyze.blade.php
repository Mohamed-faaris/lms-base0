<div>
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <flux:button variant="ghost" href="{{ route('admin.courses.show', $course->id) }}" wire:navigate icon="arrow-left" />
        <div class="flex-1">
            <flux:heading level="1" size="xl">Analyze: {{ $course->title }}</flux:heading>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Course Statistics & Analytics</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="mb-6 border-b border-zinc-200 dark:border-zinc-700">
        <nav class="flex gap-4">
            <button
                wire:click="setTab('overview')"
                class="pb-3 px-1 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}"
            >
                Overview
            </button>
            <button
                wire:click="setTab('enrollments')"
                class="pb-3 px-1 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'enrollments' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}"
            >
                Enrollments
            </button>
            <button
                wire:click="setTab('progress')"
                class="pb-3 px-1 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'progress' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}"
            >
                Progress
            </button>
            <button
                wire:click="setTab('content')"
                class="pb-3 px-1 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'content' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}"
            >
                Content
            </button>
        </nav>
    </div>

    {{-- Overview Tab --}}
    @if ($activeTab === 'overview')
        <div class="grid gap-4 md:grid-cols-4 mb-8">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                        <flux:icon.users class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['totalEnrollments'] }}</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Enrollments</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-green-500/20 flex items-center justify-center">
                        <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['completedEnrollments'] }}</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Completed</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                        <flux:icon.clock class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['inProgressEnrollments'] }}</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">In Progress</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                        <flux:icon.chart-bar class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['avgProgress'] }}%</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Avg Progress</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <flux:heading level="3" size="md" class="mb-4">Enrollment Status</flux:heading>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Completed</span>
                        <div class="flex items-center gap-2">
                            <div class="w-32 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 rounded-full" style="width: {{ $stats['totalEnrollments'] > 0 ? (int) round($stats['completedEnrollments'] / $stats['totalEnrollments'] * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $stats['completedEnrollments'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">In Progress</span>
                        <div class="flex items-center gap-2">
                            <div class="w-32 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                <div class="h-full bg-yellow-500 rounded-full" style="width: {{ $stats['totalEnrollments'] > 0 ? (int) round($stats['inProgressEnrollments'] / $stats['totalEnrollments'] * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $stats['inProgressEnrollments'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Not Started</span>
                        <div class="flex items-center gap-2">
                            <div class="w-32 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                <div class="h-full bg-zinc-400 rounded-full" style="width: {{ $stats['totalEnrollments'] > 0 ? (int) round($stats['notStartedEnrollments'] / $stats['totalEnrollments'] * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $stats['notStartedEnrollments'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <flux:heading level="3" size="md" class="mb-4">Content Breakdown</flux:heading>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Videos</span>
                        <div class="flex items-center gap-2">
                            <div class="w-32 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 rounded-full" style="width: {{ $stats['totalContent'] > 0 ? (int) round($stats['totalVideos'] / $stats['totalContent'] * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $stats['totalVideos'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Articles</span>
                        <div class="flex items-center gap-2">
                            <div class="w-32 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                <div class="h-full bg-purple-500 rounded-full" style="width: {{ $stats['totalContent'] > 0 ? (int) round($stats['totalArticles'] / $stats['totalContent'] * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $stats['totalArticles'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Quizzes</span>
                        <div class="flex items-center gap-2">
                            <div class="w-32 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 rounded-full" style="width: {{ $stats['totalContent'] > 0 ? (int) round($stats['totalQuizzes'] / $stats['totalContent'] * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $stats['totalQuizzes'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Enrollments Tab --}}
    @if ($activeTab === 'enrollments')
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Enrolled Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Progress</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($enrollmentData as $data)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                            {{ $data['user_initial'] }}
                                        </span>
                                    </div>
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $data['user_name'] }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $data['enrolled_at']?->format('M d, Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-24 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-500 rounded-full" style="width: {{ $data['progress'] }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $data['progress'] }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if ($data['progress'] === 100)
                                    <flux:badge color="green">Completed</flux:badge>
                                @elseif ($data['progress'] > 0)
                                    <flux:badge color="yellow">In Progress</flux:badge>
                                @else
                                    <flux:badge color="zinc">Not Started</flux:badge>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">No enrollments yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    {{-- Progress Tab --}}
    @if ($activeTab === 'progress')
        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <flux:heading level="3" size="md" class="mb-4">Progress Distribution</flux:heading>
                <div class="space-y-4">
                    @foreach ($progressData as $range => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $range }}</span>
                            <div class="flex items-center gap-2">
                                <div class="w-40 h-3 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                    <div 
                                        class="h-full rounded-full {{ $range === '100%' ? 'bg-green-500' : 'bg-blue-500' }}" 
                                        style="width: {{ $stats['totalEnrollments'] > 0 ? round($count / $stats['totalEnrollments'] * 100) : 0 }}%"
                                    ></div>
                                </div>
                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100 w-8 text-right">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <flux:heading level="3" size="md" class="mb-4">Completion Timeline</flux:heading>
                @if (count($completionData) > 0)
                    <div class="space-y-3">
                        @foreach ($completionData as $month => $count)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $month }}</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-40 h-3 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500 rounded-full" style="width: {{ $stats['completedEnrollments'] > 0 ? round($count / $stats['completedEnrollments'] * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $count }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">No completions yet</p>
                @endif
            </div>
        </div>
    @endif

    {{-- Content Tab --}}
    @if ($activeTab === 'content')
        <div class="grid gap-4 md:grid-cols-4 mb-6">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4 text-center">
                <flux:icon.video-camera class="h-8 w-8 mx-auto text-blue-500 mb-2" />
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['totalVideos'] }}</p>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Videos</p>
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4 text-center">
                <flux:icon.document-text class="h-8 w-8 mx-auto text-purple-500 mb-2" />
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['totalArticles'] }}</p>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Articles</p>
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4 text-center">
                <flux:icon.clipboard-document-check class="h-8 w-8 mx-auto text-green-500 mb-2" />
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['totalQuizzes'] }}</p>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Quizzes</p>
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4 text-center">
                <flux:icon.folder class="h-8 w-8 mx-auto text-zinc-500 mb-2" />
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $course->topics->count() }}</p>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Topics</p>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Topic</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Modules</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Content</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($course->topics as $topic)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900">
                            <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100 font-medium">{{ $topic->name }}</td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $topic->modules->count() }}</td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $topic->modules->sum(fn($m) => $m->contents->count()) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">No topics found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
