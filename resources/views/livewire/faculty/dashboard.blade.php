<div class="space-y-6">
    {{-- Welcome Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                Welcome back, {{ explode(' ', auth()->user()->name)[0] }}
            </h2>
            <p class="text-zinc-500 dark:text-zinc-400">Continue your learning journey</p>
        </div>
        <flux:button>
            <flux:icon.play variant="mini" class="mr-2" />
            Resume Learning
        </flux:button>
    </div>

    {{-- Gamification Stats --}}
    <div class="grid gap-4 md:grid-cols-4">
        {{-- XP Card --}}
        <div class="md:col-span-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-950 dark:to-blue-900 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-full bg-blue-500/20 flex items-center justify-center">
                        <flux:icon.bolt class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $totalXp }}</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Total XP</p>
                    </div>
                </div>
                <flux:badge color="blue" class="text-lg px-3 py-1">
                    Level {{ $level }}
                </flux:badge>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500 dark:text-zinc-400">{{ $xpToNextLevel }} XP to Level {{ $level + 1 }}</span>
                    <span class="text-zinc-900 dark:text-zinc-100 font-medium">{{ $totalXp }}/{{ $level * 500 }}</span>
                </div>
                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full" style="width: {{ ($totalXp % 500) / 5 }}%"></div>
                </div>
            </div>
        </div>

        {{-- Badge Card --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <div class="flex flex-col items-center text-center">
                <div class="h-16 w-16 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-3">
                    <flux:icon.trophy class="h-8 w-8 text-amber-600 dark:text-amber-400" />
                </div>
                <p class="font-bold text-zinc-900 dark:text-zinc-100">{{ $currentBadge?->title ?? 'Novice' }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Current Badge</p>
                @if($nextBadge)
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">
                        Next: {{ $nextBadge->title }} at Lvl {{ floor(($nextBadge->conditions['min_xp'] ?? 500) / 500) + 1 }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Streak Card --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-950 dark:to-orange-900 p-6">
            <div class="flex flex-col items-center text-center">
                <div class="h-16 w-16 rounded-full bg-orange-500/20 flex items-center justify-center mb-3">
                    <flux:icon.fire class="h-8 w-8 text-orange-600 dark:text-orange-400" />
                </div>
                <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $streak }}</p>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Day Streak</p>
                <flux:button variant="ghost" size="xs" class="mt-2">
                    View Details
                </flux:button>
            </div>
        </div>
    </div>

    {{-- Weekly Streak Calendar --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
        <div class="flex items-center justify-between p-4 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">This Week</h3>
            <flux:badge color="emerald" variant="subtle">
                <flux:icon.star class="h-3 w-3 mr-1" />
                {{ $weeklyStreak }} Week Streak
            </flux:badge>
        </div>
        <div class="flex justify-between gap-2 p-4">
            @foreach($weekDays as $index => $day)
                <div class="flex flex-col items-center gap-1">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $day['name'] }}</span>
                    <div class="h-10 w-10 rounded-full flex items-center justify-center transition-colors
                        {{ $day['isCompleted'] ? 'bg-emerald-500 text-white' : ($day['isToday'] ? 'bg-blue-100 dark:bg-blue-900 border-2 border-blue-500 text-blue-600 dark:text-blue-400' : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-400 dark:text-zinc-500') }}">
                        @if($day['isCompleted'])
                            <flux:icon.fire class="h-5 w-5" />
                        @else
                            <span class="text-sm font-medium">{{ $index + 1 }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Assigned Courses --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
        <div class="flex items-center justify-between p-4 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">My Courses</h3>
            <flux:button variant="ghost" size="sm">
                View All
                <flux:icon.chevron-right class="ml-1 h-4 w-4" />
            </flux:button>
        </div>
        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
            @forelse($enrolledCourses as $course)
                @php
                    $deadlineValue = is_numeric($course->deadline) ? $course->deadline : 0;
                    $daysLeft = $deadlineValue;
                    $isUrgent = $daysLeft <= 3 && $daysLeft > 0;
                    $isOverdue = $daysLeft < 0;
                    $isCompleted = $course->status === 'completed';
                @endphp
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-4 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors cursor-pointer">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <flux:icon.book-open class="h-4 w-4 text-blue-600 dark:text-blue-400 shrink-0" />
                            <p class="font-medium text-zinc-900 dark:text-zinc-100 truncate">{{ $course->name }}</p>
                            @if($isCompleted)
                                <flux:badge color="emerald" size="sm">Completed</flux:badge>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 text-sm text-zinc-500 dark:text-zinc-400">
                            <span>{{ $course->completedModules }}/{{ $course->modules }} modules</span>
                            <span class="flex items-center gap-1">
                                <flux:icon.bolt class="h-3 w-3" />
                                {{ $course->xpReward }} XP
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 sm:w-[200px]">
                        <div class="flex-1 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full" style="width: {{ $course->progress }}%"></div>
                        </div>
                        <span class="text-sm font-medium w-12 text-right">{{ $course->progress }}%</span>
                    </div>

                    <div class="flex items-center gap-2 sm:w-[120px]">
                        @if($isCompleted)
                            <flux:badge color="emerald" variant="subtle" size="sm">
                                <flux:icon.check-circle class="h-3 w-3 mr-1" />
                                Done
                            </flux:badge>
                        @elseif($isOverdue)
                            <flux:badge color="red" size="sm">
                                <flux:icon.clock class="h-3 w-3 mr-1" />
                                Overdue
                            </flux:badge>
                        @elseif($isUrgent)
                            <flux:badge color="amber" size="sm">
                                <flux:icon.clock class="h-3 w-3 mr-1" />
                                {{ $daysLeft }}d left
                            </flux:badge>
                        @else
                            <span class="flex items-center gap-1 text-sm text-zinc-500 dark:text-zinc-400">
                                <flux:icon.calendar class="h-3 w-3" />
                                {{ $daysLeft }}d left
                            </span>
                        @endif
                    </div>

                    <flux:button size="sm" :variant="$isCompleted ? 'outline' : 'primary'" class="shrink-0">
                        {{ $isCompleted ? 'Review' : ($course->progress > 0 ? 'Continue' : 'Start') }}
                    </flux:button>
                </div>
            @empty
                <div class="p-8 text-center text-zinc-500 dark:text-zinc-400">
                    No courses enrolled yet.
                </div>
            @endforelse
        </div>
    </div>
</div>
