<div class="max-w-md mx-auto space-y-6">
    {{-- Main Streak Display --}}
    <div class="text-center py-8">
        <div class="relative inline-flex">
            <div class="h-32 w-32 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center">
                <flux:icon.fire class="h-16 w-16 text-white" />
            </div>
            <div class="absolute -bottom-2 -right-2 bg-white dark:bg-zinc-800 rounded-full p-1">
                <flux:badge color="orange" class="text-lg px-3 py-1">
                    {{ $streak }}
                </flux:badge>
            </div>
        </div>
        <h2 class="text-3xl font-bold text-zinc-900 dark:text-zinc-100 mt-4">Day Streak</h2>
        <p class="text-zinc-500 dark:text-zinc-400">Keep learning to maintain your streak!</p>
    </div>

    {{-- Weekly Progress --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">This Week</h3>
            <flux:badge color="emerald" variant="subtle">
                <flux:icon.trophy class="h-3 w-3 mr-1" />
                Week {{ $weeklyStreak }}
            </flux:badge>
        </div>
        <div class="flex justify-between gap-1">
            @foreach($weekData as $day)
                <div class="flex flex-col items-center gap-2">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $day['day'] }}</span>
                    <div class="h-12 w-12 rounded-full flex items-center justify-center transition-all 
                        {{ $day['completed'] ? 'bg-gradient-to-br from-orange-400 to-orange-600' : 
                           ($day['isToday'] ? 'bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-500 border-dashed' : 
                           'bg-zinc-100 dark:bg-zinc-700') }}">
                        @if($day['completed'])
                            <flux:icon.fire class="h-6 w-6 text-white" />
                        @elseif($day['isToday'])
                            <span class="text-blue-600 dark:text-blue-400 font-medium">?</span>
                        @else
                            <span class="text-zinc-400 dark:text-zinc-500 text-sm">{{ $day['index'] + 1 }}</span>
                        @endif
                    </div>
                    @if($day['xp'] > 0)
                        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">+{{ $day['xp'] }}</span>
                    @else
                        <span class="text-xs text-transparent select-none">+0</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- XP from Streaks --}}
    <div class="rounded-xl border border-blue-200 dark:border-blue-800 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-full bg-blue-500/20 flex items-center justify-center">
                    <flux:icon.bolt class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">+{{ $xpThisWeek }}</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">XP this week</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Streak Bonus</p>
                <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">+50 XP/day</p>
            </div>
        </div>
    </div>

    {{-- Streak Milestones --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Streak Rewards</h3>
        <div class="space-y-3">
            @foreach($streakRewards as $reward)
                <div class="flex items-center justify-between p-3 rounded-lg border {{ $reward['achieved'] ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800' : 'bg-zinc-50 dark:bg-zinc-800/50 border-zinc-200 dark:border-zinc-700' }}">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full flex items-center justify-center {{ $reward['achieved'] ? 'bg-emerald-100 dark:bg-emerald-800 text-emerald-600 dark:text-emerald-400' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400' }}">
                            @if($reward['achieved'])
                                <flux:icon.fire class="h-5 w-5" />
                            @else
                                <flux:icon.calendar class="h-5 w-5" />
                            @endif
                        </div>
                        <div>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $reward['days'] }} Day Streak</p>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $reward['achieved'] ? 'Achieved!' : ($reward['days'] - $streak) . ' days to go' }}
                            </p>
                        </div>
                    </div>
                    <flux:badge color="{{ $reward['achieved'] ? 'emerald' : 'zinc' }}">
                        <flux:icon.bolt class="h-3 w-3 mr-1" />
                        {{ $reward['xp'] }} XP
                    </flux:badge>
                </div>
            @endforeach
        </div>
    </div>
</div>
