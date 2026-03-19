<div class="space-y-8 pb-10">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">My Learning Journey</h1>
            <p class="text-zinc-500 dark:text-zinc-400 mt-2 text-lg">Track your progress and continue mastering your courses.</p>
        </div>
        <div class="flex items-center gap-2">
            <flux:badge color="blue" class="shadow-sm">
                <flux:icon.book-open class="h-4 w-4 mr-2" />
                {{ $enrolledCourses->count() }} Enrolled
            </flux:badge>
        </div>
    </div>

    {{-- Courses Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8">
        @forelse($enrolledCourses as $course)
            @php
                $deadlineValue = is_numeric($course->deadline) ? $course->deadline : 0;
                $daysLeft = $deadlineValue;
                $isUrgent = $daysLeft <= 3 && $daysLeft > 0;
                $isOverdue = $daysLeft < 0;
                $isCompleted = $course->status === 'completed';
            @endphp
            <div class="group flex flex-col bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm hover:shadow-xl hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-300 overflow-hidden relative hover:-translate-y-1">
                
                {{-- Decorative background elements --}}
                <div class="absolute inset-0 bg-gradient-to-br {{ $isCompleted ? 'from-emerald-50/50 to-transparent dark:from-emerald-900/10' : 'from-blue-50/50 to-transparent dark:from-blue-900/10' }} opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                @if($isCompleted)
                    <div class="absolute top-0 right-0 w-40 h-40 bg-emerald-400/10 dark:bg-emerald-500/10 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none transition-transform group-hover:scale-110"></div>
                @else
                    <div class="absolute top-0 right-0 w-40 h-40 bg-blue-400/10 dark:bg-blue-500/10 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none transition-transform group-hover:scale-110"></div>
                @endif

                {{-- Top Section: Icon & Status --}}
                <div class="p-6 pb-4 flex items-start justify-between gap-4 relative z-10">
                    <div class="h-16 w-16 rounded-2xl {{ $isCompleted ? 'bg-emerald-100/80 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400' : 'bg-blue-100/80 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400' }} flex items-center justify-center shrink-0 shadow-inner ring-1 ring-white/50 dark:ring-white/5 transition-transform group-hover:scale-105 duration-300">
                        @if($isCompleted)
                            <flux:icon.check-badge class="h-8 w-8" />
                        @else
                            <flux:icon.book-open class="h-8 w-8" />
                        @endif
                    </div>
                    
                    <div class="flex flex-col items-end gap-2">
                        @if($isCompleted)
                            <flux:badge color="emerald" class="font-semibold shadow-sm px-3 py-1">Completed</flux:badge>
                        @elseif($isOverdue)
                            <flux:badge color="red" class="font-semibold shadow-sm animate-pulse px-3 py-1">Overdue</flux:badge>
                        @elseif($isUrgent)
                            <flux:badge color="amber" class="font-semibold shadow-sm px-3 py-1">{{ $daysLeft }} days left</flux:badge>
                        @else
                            <flux:badge color="zinc" class="font-medium bg-zinc-100 dark:bg-zinc-800 px-3 py-1">{{ $daysLeft }} days left</flux:badge>
                        @endif
                    </div>
                </div>
                
                {{-- Middle Section: Details --}}
                <div class="px-6 flex-1 relative z-10 flex flex-col">
                    <h3 class="font-bold text-xl text-zinc-900 dark:text-zinc-100 line-clamp-2 leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" title="{{ $course->name }}">
                        {{ $course->name }}
                    </h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-3 line-clamp-2 leading-relaxed flex-1" title="{{ $course->description }}">
                        {{ $course->description }}
                    </p>

                    <div class="flex items-center gap-3 mt-6 py-5 border-t border-zinc-100 dark:border-zinc-800 text-sm font-medium">
                        <div class="flex items-center gap-2 bg-zinc-50 dark:bg-zinc-800/50 text-zinc-600 dark:text-zinc-300 px-3 py-1.5 rounded-lg border border-zinc-200/50 dark:border-zinc-700/50">
                            <flux:icon.squares-2x2 class="h-4 w-4 text-zinc-400 dark:text-zinc-500" />
                            <span>{{ $course->completedModules }}/{{ $course->modules }} <span class="hidden sm:inline">Mods</span></span>
                        </div>
                        <div class="flex items-center gap-2 bg-amber-50/80 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 px-3 py-1.5 rounded-lg border border-amber-200/50 dark:border-amber-800/50">
                            <flux:icon.bolt class="h-4 w-4 text-amber-500" />
                            <span>{{ $course->xpReward }} <span class="hidden sm:inline">XP</span></span>
                        </div>
                    </div>
                </div>

                {{-- Bottom Section: Progress & Action --}}
                <div class="px-6 pb-6 pt-2 relative z-10 bg-gradient-to-b from-transparent to-zinc-50/50 dark:to-zinc-900/50">
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-end">
                            <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Course Progress</span>
                            <span class="text-xl font-black tracking-tight {{ $isCompleted ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-900 dark:text-zinc-100' }}">{{ $course->progress }}%</span>
                        </div>
                        <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-3 shadow-inner overflow-hidden p-0.5">
                            <div class="h-full rounded-full transition-all duration-1000 relative {{ $isCompleted ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.4)]' : 'bg-blue-600 shadow-[0_0_10px_rgba(37,99,235,0.4)]' }}" style="width: {{ $course->progress }}%">
                                <div class="absolute inset-0 bg-white/20 w-full h-full" style="background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent); background-size: 1rem 1rem;"></div>
                            </div>
                        </div>
                    </div>

                    <flux:button 
                        variant="{{ $isCompleted ? 'outline' : 'primary' }}" 
                        class="w-full h-12 text-base font-semibold transition-all duration-300 {{ $isCompleted ? 'hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-200 dark:hover:border-emerald-800' : 'shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:-translate-y-0.5' }}" 
                        href="{{ route('faculty.course-player', ['course' => $course->id]) }}" 
                        wire:navigate
                    >
                        @if($isCompleted)
                            <flux:icon.arrow-path class="w-5 h-5 mr-2" />
                            Review Course
                        @elseif($course->progress > 0)
                            <flux:icon.play class="w-5 h-5 mr-2" />
                            Continue Learning
                        @else
                            <flux:icon.play class="w-5 h-5 mr-2" />
                            Start Course
                        @endif
                    </flux:button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 px-6 flex flex-col items-center justify-center text-center bg-white dark:bg-zinc-900 rounded-3xl border-2 border-dashed border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="h-28 w-28 bg-zinc-50 dark:bg-zinc-800/50 rounded-full shadow-inner border border-zinc-100 dark:border-zinc-700/50 flex items-center justify-center mb-6 relative">
                    <div class="absolute inset-0 bg-blue-400/10 rounded-full blur-xl animate-pulse"></div>
                    <flux:icon.academic-cap class="h-14 w-14 text-zinc-300 dark:text-zinc-600 relative z-10" />
                </div>
                <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-3">No courses enrolled yet</h3>
                <p class="text-zinc-500 dark:text-zinc-400 max-w-md mx-auto text-lg leading-relaxed">
                    You haven't been assigned to any learning paths. When you are enrolled in a course, your journey will begin here.
                </p>
                <flux:button variant="outline" class="mt-8 font-semibold" href="{{ route('faculty.dashboard') }}" wire:navigate>
                    <flux:icon.arrow-left class="w-4 h-4 mr-2" />
                    Return to Dashboard
                </flux:button>
            </div>
        @endforelse
    </div>
</div>
