<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Our Courses</h1>
        <p class="text-zinc-500 dark:text-zinc-400 mt-1">Browse and enroll in available courses</p>
    </div>

    <div class="mb-6">
        <flux:input 
            wire:model.live="search" 
            placeholder="Search courses..." 
            icon="magnifying-glass"
        />
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($courses as $course)
            <a href="#" class="block group">
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6 hover:border-blue-500 dark:hover:border-blue-500 transition-colors">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                        {{ $course->title }}
                    </h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 line-clamp-2">
                        {{ $course->description ?: 'No description available.' }}
                    </p>
                    <div class="mt-4 flex items-center justify-between text-sm text-zinc-500 dark:text-zinc-400">
                        <span>{{ $course->enrollments_count }} enrolled</span>
                        <span>{{ $course->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-zinc-500 dark:text-zinc-400">No courses found.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $courses->links() }}
    </div>
</div>