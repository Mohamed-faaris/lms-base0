<div class="space-y-8 pb-10">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">My Learning Journey</h1>
            <p class="text-zinc-500 dark:text-zinc-400 mt-2 text-lg">Track your progress and continue mastering your courses.</p>
        </div>
        <div class="flex items-center gap-2">
            <flux:badge color="blue" size="lg" class="shadow-sm">
                <flux:icon.book-open class="h-4 w-4 mr-2" />
                {{ $enrolledCourses->count() }} Enrolled
            </flux:badge>
        </div>
    </div>

    {{-- Courses Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        @forelse($enrolledCourses as $course)
            @php
                $deadlineValue = is_numeric($course->deadline) ? $course->deadline : 0;
                $daysLeft = $deadlineValue;
                $isUrgent = $daysLeft <= 3 && $daysLeft > 0;
                $isOverdue = $daysLeft < 0;
                $isCompleted = $course->status === 'completed';
            @endphp
            <div class="group flex flex-col bg-white dark:bg-zinc-800/80 rounded-2xl border border-zinc-200 dark:border-zinc-700/80 shadow-sm hover:shadow-xl hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 overflow-hidden relative hover:-translate-y-1">
                
                {{-- Decorative background element --}}
                @if($isCompleted)
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-400/10 dark:bg-emerald-400/5 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none"></div>
                @else
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-400/10 dark:bg-blue-400/5 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none"></div>
                @endif

                {{-- Top Section: Icon & Status --}}
                <div class="p-6 pb-4 flex items-start justify-between gap-4 relative z-10">
                    <div class="h-14 w-14 rounded-xl {{ $isCompleted ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' }} flex items-center justify-center shrink-0 shadow-inner">
                        @if($isCompleted)
                            <flux:icon.check-badge class="h-7 w-7" />
                        @else
                            <flux:icon.book-open class="h-7 w-7" />
                        @endif
                    </div>
                    
                    <div class="flex flex-col items-end gap-2">
                        @if($isCompleted)
                            <flux:badge color="emerald" class="font-semibold shadow-sm">Completed</flux:badge>
                        @elseif($isOverdue)
                            <flux:badge color="red" class="font-semibold shadow-sm animate-pulse">Overdue</flux:badge>
                        @elseif($isUrgent)
                            <flux:badge color="amber" class="font-semibold shadow-sm">{{ $daysLeft }} days left</flux:badge>
                        @else
                            <flux:badge color="zinc" class="font-medium bg-zinc-100 dark:bg-zinc-800">{{ $daysLeft }} days left</flux:badge>
                        @endif
                    </div>
                </div>
                
                {{-- Middle Section: Details --}}
                <div class="px-6 flex-1 relative z-10 flex flex-col">
                    <h3 class="font-bold text-xl text-zinc-900 dark:text-zinc-100 line-clamp-2 leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" title="{{ $course->name }}">
                        {{ $course->name }}
                    </h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 line-clamp-2 leading-relaxed flex-1" title="{{ $course->description }}">
                        {{ $course->description }}
                    </p>

                    <div class="flex items-center gap-4 mt-6 py-4 border-t border-zinc-100 dark:border-zinc-700/50 text-sm text-zinc-600 dark:text-zinc-300 font-medium">
                        <span class="flex items-center gap-1.5 bg-zinc-50 dark:bg-zinc-800 px-2 py-1 rounded-md">
                            <flux:icon.squares-2x2 class="h-4 w-4 text-zinc-400" />
                            {{ $course->completedModules }}/{{ $course->modules }} Mods
                        </span>
                        <span class="flex items-center gap-1.5 bg-amber-50 dark:bg-amber-900/10 text-amber-700 dark:text-amber-500 px-2 py-1 rounded-md">
                            <flux:icon.bolt class="h-4 w-4 text-amber-500" />
                            {{ $course->xpReward }} XP
                        </span>
                    </div>
                </div>

                {{-- Bottom Section: Progress & Action --}}
                <div class="px-6 pb-6 pt-2 relative z-10">
                    <div class="space-y-2 mb-5">
                        <div class="flex justify-between items-end">
                            <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Course Progress</span>
                            <span class="text-lg font-bold {{ $isCompleted ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-900 dark:text-zinc-100' }}">{{ $course->progress }}%</span>
                        </div>
                        <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-2.5 shadow-inner overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-1000 relative {{ $isCompleted ? 'bg-emerald-500' : 'bg-blue-600' }}" style="width: {{ $course->progress }}%">
                                <div class="absolute inset-0 bg-white/20"></div>
                            </div>
                        </div>
                    </div>

                    <flux:button 
                        variant="{{ $isCompleted ? 'outline' : 'primary' }}" 
                        class="w-full font-semibold {{ $isCompleted ? '' : 'shadow-md shadow-blue-500/20' }}" 
                        href="{{ route('faculty.course-player', ['course' => $course->id]) }}" 
                        wire:navigate
                    >
                        @if($isCompleted)
                            <flux:icon.arrow-path class="w-4 h-4 mr-2" />
                            Review Course
                        @elseif($course->progress > 0)
                            <flux:icon.play class="w-4 h-4 mr-2" />
                            Continue Learning
                        @else
                            <flux:icon.play class="w-4 h-4 mr-2" />
                            Start Course
                        @endif
                    </flux:button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 px-6 flex flex-col items-center justify-center text-center bg-zinc-50/50 dark:bg-zinc-800/20 rounded-3xl border-2 border-dashed border-zinc-200 dark:border-zinc-700">
                <div class="h-24 w-24 bg-white dark:bg-zinc-800 rounded-full shadow-sm border border-zinc-100 dark:border-zinc-700 flex items-center justify-center mb-6">
                    <flux:icon.academic-cap class="h-12 w-12 text-zinc-300 dark:text-zinc-600" />
                </div>
                <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-2">No courses enrolled yet</h3>
                <p class="text-zinc-500 dark:text-zinc-400 max-w-md mx-auto text-lg">
                    You haven't been assigned to any learning paths. When you are enrolled in a course, it will appear here.
                </p>
                <flux:button variant="outline" class="mt-8" href="{{ route('faculty.dashboard') }}" wire:navigate>
                    Return to Dashboard
                </flux:button>
            </div>
        @endforelse
    </div>
</div>
