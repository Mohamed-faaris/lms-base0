<?php

use App\Models\CourseEnrollment;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public Collection $enrollments;

    public function mount(): void
    {
        $this->enrollments = CourseEnrollment::with([
            'courseVersion.course',
        ])
            ->where('student_id', auth()->id())
            ->get();
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Learning
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($enrollments->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    You are not enrolled in any courses.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($enrollments as $enrollment)
                        @php
                            $course = $enrollment->courseVersion->course;
                            $progressCount = $enrollment->progress()->count();
                            $moduleItemCount = $enrollment->courseVersion->modules()->withCount('items')->get()->sum('items_count');
                        @endphp
                        <a href="{{ route('learner.my-learning.course', $course->slug) }}" wire:navigate
                           class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                            @if ($course->thumbnail)
                                <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-full h-40 object-cover">
                            @else
                                <div class="w-full h-40 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-white text-3xl font-bold">{{ substr($course->title, 0, 2) }}</span>
                                </div>
                            @endif
                            <div class="p-5">
                                <h3 class="font-semibold text-lg text-gray-900">{{ $course->title }}</h3>
                                <p class="mt-1 text-sm text-gray-500">{{ Str::limit($course->description, 120) }}</p>
                                <div class="mt-4 flex items-center justify-between text-sm text-gray-600">
                                    <span class="capitalize">{{ str_replace('_', ' ', $enrollment->status->value) }}</span>
                                    <span>{{ $progressCount }} / {{ $moduleItemCount }} items</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
