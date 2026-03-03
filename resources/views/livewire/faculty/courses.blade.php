<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">My Courses</h2>
            <p class="text-zinc-500 dark:text-zinc-400">Manage and continue your enrolled courses</p>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse($enrolledCourses as $course)
            @php
                $deadlineValue = is_numeric($course->deadline) ? $course->deadline : 0;
                $daysLeft = $deadlineValue;
                $isUrgent = $daysLeft <= 3 && $daysLeft > 0;
                $isOverdue = $daysLeft < 0;
                $isCompleted = $course->status === 'completed';
            @endphp
            <div class="flex flex-col rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-hidden hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
                <div class="p-5 flex-1 space-y-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="h-10 w-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center shrink-0">
                            <flux:icon.book-open class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        @if($isCompleted)
                            <flux:badge color="emerald" size="sm">Completed</flux:badge>
                        @elseif($isOverdue)
                            <flux:badge color="red" size="sm">Overdue</flux:badge>
                        @elseif($isUrgent)
                            <flux:badge color="amber" size="sm">{{ $daysLeft }}d left</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">{{ $daysLeft }}d left</flux:badge>
                        @endif
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-lg text-zinc-900 dark:text-zinc-100 line-clamp-1" title="{{ $course->name }}">
                            {{ $course->name }}
                        </h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 line-clamp-2" title="{{ $course->description }}">
                            {{ $course->description }}
                        </p>
                    </div>

                    <div class="flex items-center gap-4 text-sm text-zinc-500 dark:text-zinc-400">
                        <span class="flex items-center gap-1">
                            <flux:icon.document-text class="h-4 w-4" />
                            {{ $course->completedModules }}/{{ $course->modules }} mods
                        </span>
                        <span class="flex items-center gap-1">
                            <flux:icon.bolt class="h-4 w-4 text-blue-500" />
                            {{ $course->xpReward }} XP
                        </span>
                    </div>

                    <div class="space-y-1.5 pt-2">
                        <div class="flex justify-between text-xs">
                            <span class="text-zinc-500 dark:text-zinc-400">Progress</span>
                            <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $course->progress }}%</span>
                        </div>
                        <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $course->progress }}%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 border-t border-zinc-100 dark:border-zinc-700/50 bg-zinc-50/50 dark:bg-zinc-800/50">
                    <flux:button 
                        variant="{{ $isCompleted ? 'outline' : 'primary' }}" 
                        class="w-full" 
                        href="{{ route('faculty.course-player', ['course' => $course->id]) }}" 
                        wire:navigate
                    >
                        @if($isCompleted)
                            Review Course
                        @elseif($course->progress > 0)
                            Continue Learning
                        @else
                            Start Course
                        @endif
                    </flux:button>
                </div>
            </div>
        @empty
            <div class="col-span-full p-8 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-xl">
                <flux:icon.academic-cap class="h-12 w-12 text-zinc-400 dark:text-zinc-500 mx-auto mb-3" />
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">No courses yet</h3>
                <p class="text-zinc-500 dark:text-zinc-400 mt-1">You haven't been enrolled in any courses.</p>
            </div>
        @endforelse
    </div>
</div>
