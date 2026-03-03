<div class="space-y-8">
    {{-- Welcome Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-zinc-800 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm relative overflow-hidden">
        {{-- Decorative background --}}
        <div class="absolute right-0 top-0 w-64 h-full bg-gradient-to-l from-blue-50 to-transparent dark:from-blue-900/20 pointer-events-none"></div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                Welcome back, {{ explode(' ', auth()->user()->name)[0] }} 👋
            </h2>
            <p class="text-zinc-500 dark:text-zinc-400 mt-1">Ready to continue your learning journey today?</p>
        </div>
        <div class="relative z-10 shrink-0">
            <flux:button variant="primary" href="{{ route('faculty.courses') }}" wire:navigate class="shadow-sm">
                <flux:icon.play-circle class="w-5 h-5 mr-2" />
                Resume Learning
            </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Left Column --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Weekly Streak Calendar --}}
            <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between p-5 border-b border-zinc-100 dark:border-zinc-700/50">
                    <div>
                        <h3 class="font-semibold text-lg text-zinc-900 dark:text-zinc-100">Learning Activity</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Your engagement this week</p>
                    </div>
                    <flux:badge color="emerald" class="px-3 py-1 text-sm shadow-sm">
                        <flux:icon.star class="h-4 w-4 mr-1.5" />
                        {{ $weeklyStreak }} Week Streak
                    </flux:badge>
                </div>
                <div class="p-6 bg-zinc-50/50 dark:bg-zinc-900/20">
                    <div class="flex justify-between gap-2 max-w-lg mx-auto">
                        @foreach($weekDays as $index => $day)
                            <div class="flex flex-col items-center gap-3">
                                <div class="h-12 w-12 rounded-full flex items-center justify-center transition-all shadow-sm
                                    {{ $day['isCompleted'] ? 'bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-emerald-200 dark:shadow-none' : 
                                    ($day['isToday'] ? 'bg-white dark:bg-zinc-800 border-2 border-blue-500 text-blue-600 dark:text-blue-400 ring-4 ring-blue-50 dark:ring-blue-900/20' : 
                                    'bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-400') }}">
                                    @if($day['isCompleted'])
                                        <flux:icon.check class="h-6 w-6" />
                                    @elseif($day['isToday'])
                                        <flux:icon.fire class="h-6 w-6 opacity-50" />
                                    @else
                                        <span class="text-sm font-medium">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                <span class="text-xs font-medium {{ $day['isToday'] ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-500 dark:text-zinc-400' }}">
                                    {{ $day['name'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Assigned Courses --}}
            <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between p-5 border-b border-zinc-100 dark:border-zinc-700/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <flux:icon.book-open class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <h3 class="font-semibold text-lg text-zinc-900 dark:text-zinc-100">My Enrolled Courses</h3>
                    </div>
                    <flux:button variant="subtle" size="sm" href="{{ route('faculty.courses') }}" wire:navigate class="font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700">
                        View all <span class="sr-only">courses</span>
                        <flux:icon.arrow-right class="ml-1.5 h-4 w-4" />
                    </flux:button>
                </div>
                
                <div class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                    @forelse($enrolledCourses->take(3) as $course)
                        @php
                            $deadlineValue = is_numeric($course->deadline) ? $course->deadline : 0;
                            $daysLeft = $deadlineValue;
                            $isUrgent = $daysLeft <= 3 && $daysLeft > 0;
                            $isOverdue = $daysLeft < 0;
                            $isCompleted = $course->status === 'completed';
                        @endphp
                        <div class="flex flex-col sm:flex-row sm:items-center gap-5 p-5 hover:bg-zinc-50 dark:hover:bg-zinc-700/30 transition-colors group">
                            
                            {{-- Course Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1.5">
                                    <p class="font-semibold text-zinc-900 dark:text-zinc-100 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        {{ $course->name }}
                                    </p>
                                    @if($isCompleted)
                                        <flux:badge color="emerald" size="sm">Completed</flux:badge>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-zinc-500 dark:text-zinc-400">
                                    <span class="flex items-center gap-1.5">
                                        <flux:icon.squares-2x2 class="h-4 w-4 opacity-70" />
                                        {{ $course->completedModules }}/{{ $course->modules }} modules
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <flux:icon.bolt class="h-4 w-4 text-amber-500" />
                                        {{ $course->xpReward }} XP
                                    </span>
                                </div>
                            </div>

                            {{-- Progress Bar --}}
                            <div class="flex items-center gap-3 sm:w-[180px]">
                                <div class="flex-1 h-2 bg-zinc-100 dark:bg-zinc-700 rounded-full overflow-hidden shadow-inner">
                                    <div class="h-full bg-blue-600 rounded-full transition-all duration-500 relative" style="width: {{ $course->progress }}%">
                                        <div class="absolute inset-0 bg-white/20"></div>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold w-10 text-right text-zinc-700 dark:text-zinc-300">{{ $course->progress }}%</span>
                            </div>

                            {{-- Action --}}
                            <div class="flex items-center justify-between sm:justify-end gap-4 sm:w-[220px]">
                                <div class="shrink-0">
                                    @if($isCompleted)
                                        <span class="flex items-center gap-1.5 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                            <flux:icon.check-badge class="h-4 w-4" />
                                            Done
                                        </span>
                                    @elseif($isOverdue)
                                        <span class="flex items-center gap-1.5 text-sm font-medium text-red-600 dark:text-red-400">
                                            <flux:icon.exclamation-circle class="h-4 w-4" />
                                            Overdue
                                        </span>
                                    @elseif($isUrgent)
                                        <span class="flex items-center gap-1.5 text-sm font-medium text-amber-600 dark:text-amber-500">
                                            <flux:icon.clock class="h-4 w-4" />
                                            {{ $daysLeft }}d left
                                        </span>
                                    @else
                                        <span class="flex items-center gap-1.5 text-sm text-zinc-500 dark:text-zinc-400">
                                            <flux:icon.calendar class="h-4 w-4 opacity-70" />
                                            {{ $daysLeft }}d left
                                        </span>
                                    @endif
                                </div>

                                <flux:button size="sm" :variant="$isCompleted ? 'outline' : 'primary'" class="shrink-0" href="{{ route('faculty.course-player', ['course' => $course->id]) }}" wire:navigate>
                                    {{ $isCompleted ? 'Review' : ($course->progress > 0 ? 'Continue' : 'Start') }}
                                </flux:button>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 flex flex-col items-center justify-center text-center">
                            <div class="h-16 w-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                                <flux:icon.academic-cap class="h-8 w-8 text-zinc-400" />
                            </div>
                            <h4 class="text-base font-medium text-zinc-900 dark:text-zinc-100">No active courses</h4>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 max-w-sm">You haven't been assigned any courses yet. Check back later.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right Column (Gamification) --}}
        <div class="space-y-6">
            
            {{-- XP Card --}}
            <div class="rounded-2xl border border-blue-200 dark:border-blue-900/50 bg-gradient-to-b from-blue-50 to-white dark:from-blue-950/40 dark:to-zinc-900 p-6 shadow-sm relative overflow-hidden">
                {{-- Decorative shine --}}
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-400/20 blur-2xl rounded-full"></div>
                
                <div class="flex items-start justify-between mb-6 relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/20">
                            <flux:icon.bolt class="h-6 w-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total XP</p>
                            <p class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">{{ number_format($totalXp) }}</p>
                        </div>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 font-bold px-3 py-1.5 rounded-lg text-sm border border-blue-200 dark:border-blue-800">
                        Lvl {{ $level }}
                    </div>
                </div>

                <div class="space-y-2 relative z-10">
                    <div class="flex justify-between text-sm font-medium">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ $xpToNextLevel }} XP to Next Level</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $totalXp }} / {{ $level * 500 }}</span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-800 rounded-full h-2.5 shadow-inner overflow-hidden">
                        <div class="bg-blue-600 h-full rounded-full transition-all duration-1000 relative" style="width: {{ ($totalXp % 500) / 5 }}%">
                            <div class="absolute inset-0 bg-white/20"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Streak Card --}}
                <div class="rounded-2xl border border-orange-200 dark:border-orange-900/50 bg-gradient-to-b from-orange-50 to-white dark:from-orange-950/40 dark:to-zinc-900 p-5 shadow-sm flex flex-col items-center text-center hover:-translate-y-1 transition-transform duration-300">
                    <div class="h-14 w-14 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 text-white flex items-center justify-center mb-3 shadow-lg shadow-orange-500/30">
                        <flux:icon.fire class="h-7 w-7" />
                    </div>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white leading-none mb-1">{{ $streak }}</p>
                    <p class="text-xs font-medium text-orange-600 dark:text-orange-400 uppercase tracking-wider mb-3">Day Streak</p>
                    
                    <a href="{{ route('faculty.streaks') }}" class="text-xs text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-200 font-medium underline decoration-zinc-300 underline-offset-2 hover:decoration-zinc-900 transition-colors">
                        View Details
                    </a>
                </div>

                {{-- Badge Card --}}
                <div class="rounded-2xl border border-amber-200 dark:border-amber-900/50 bg-gradient-to-b from-amber-50 to-white dark:from-amber-950/40 dark:to-zinc-900 p-5 shadow-sm flex flex-col items-center text-center hover:-translate-y-1 transition-transform duration-300">
                    <div class="h-14 w-14 rounded-full bg-gradient-to-br from-amber-300 to-amber-500 text-amber-950 flex items-center justify-center mb-3 shadow-lg shadow-amber-500/30">
                        <flux:icon.trophy class="h-7 w-7" />
                    </div>
                    <p class="text-lg font-bold text-zinc-900 dark:text-white leading-tight mb-1 truncate w-full px-2" title="{{ $currentBadge?->title ?? 'Novice' }}">
                        {{ $currentBadge?->title ?? 'Novice' }}
                    </p>
                    <p class="text-[10px] font-medium text-amber-700 dark:text-amber-500 uppercase tracking-wider mb-2">Current Rank</p>
                    
                    @if($nextBadge)
                        <div class="mt-auto bg-white/60 dark:bg-black/20 w-full py-1.5 rounded text-[10px] font-medium text-zinc-600 dark:text-zinc-400 border border-amber-100 dark:border-amber-900/30">
                            Next: {{ $nextBadge->title }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
