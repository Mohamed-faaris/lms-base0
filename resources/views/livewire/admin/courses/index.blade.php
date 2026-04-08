<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <flux:heading level="1" size="xl">Courses</flux:heading>
        <flux:button href="{{ route('admin.courses.create') }}" wire:navigate>
            <flux:icon.plus variant="mini" class="mr-2" />
            Create Course
        </flux:button>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('success'))
        <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <div class="flex-1">
            <flux:input 
                wire:model.live="search" 
                placeholder="Search courses..." 
                icon="magnifying-glass"
            />
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
        <table class="w-full">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Slug</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Enrollments</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Created</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($courses as $course)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                        <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $course->id }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.courses.show', $course->id) }}" class="text-sm font-medium text-zinc-900 dark:text-zinc-100 hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $course->title }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $course->slug }}</td>
                        <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $course->enrollments_count }}</td>
                        <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400">{{ $course->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button variant="ghost" size="sm" href="{{ route('admin.courses.show', $course->id) }}" wire:navigate>
                                    View
                                </flux:button>
                                <flux:button variant="ghost" size="sm" href="{{ route('admin.courses.edit', $course->id) }}" wire:navigate>
                                    Edit
                                </flux:button>
                                <flux:button variant="ghost" size="sm" wire:click="deleteCourse({{ $course->id }})" onclick="return confirm('Are you sure you want to delete this course?')">
                                    <span class="text-red-600">Delete</span>
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                            No courses found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $courses->links() }}
    </div>
</div>