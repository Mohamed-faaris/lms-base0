<?php

use App\Models\Course;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    #[Computed]
    public function courses(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Course::with('createdBy')
            ->withCount('versions')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    public function deleteCourse(int $courseId): void
    {
        $course = Course::findOrFail($courseId);

        $title = $course->title;

        $course->delete();

        $this->resetPage();

        session()->flash('status', "Course \"{$title}\" was deleted.");
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Courses</h2>
            <a href="{{ route('admin.courses.create') }}" wire:navigate
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-dynamic-component :component="'lucide-plus'" class="w-4 h-4" />
                New Course
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Versions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($this->courses as $course)
                                <tr wire:key="course-{{ $course->id }}" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.courses.curriculum', $course->slug) }}" wire:navigate
                                           class="block text-sm font-medium text-gray-900 hover:text-indigo-600">
                                            {{ $course->title }}
                                            <span class="block text-sm text-gray-500 font-normal">{{ $course->slug }}</span>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span @class([
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            'bg-yellow-100 text-yellow-800' => $course->status === \App\Enums\CourseStatus::DRAFT,
                                            'bg-green-100 text-green-800' => $course->status === \App\Enums\CourseStatus::PUBLISHED,
                                            'bg-gray-100 text-gray-800' => $course->status === \App\Enums\CourseStatus::ARCHIVED,
                                        ])>
                                            {{ ucfirst($course->status->value) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $course->versions_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $course->createdBy?->name ?? '&mdash;' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="inline-flex items-center gap-3">
                                            <a href="{{ route('admin.courses.edit', $course->slug) }}" wire:navigate
                                               class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900">
                                                <x-dynamic-component :component="'lucide-pencil'" class="w-4 h-4" />
                                                <!-- <span>Edit</span> -->
                                            </a>
                                            <button type="button"
                                                wire:click="deleteCourse({{ $course->id }})"
                                                wire:confirm="Delete '{{ $course->title }}'? This action cannot be undone."
                                                class="inline-flex items-center gap-1 text-red-600 hover:text-red-900">
                                                <x-dynamic-component :component="'lucide-trash-2'" class="w-4 h-4" />
                                                <!-- <span>Delete</span> -->
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                        No courses yet.
                                        <a href="{{ route('admin.courses.create') }}" wire:navigate class="text-indigo-600 hover:text-indigo-800 ml-1">Create the first one.</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($this->courses->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $this->courses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
