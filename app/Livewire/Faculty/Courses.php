<?php

namespace App\Livewire\Faculty;

use App\Models\Enrollment;
use App\Models\Progress;
use Illuminate\Support\Collection;
use Livewire\Component;

class Courses extends Component
{
    public Collection $enrolledCourses;

    public function mount()
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        // Get enrolled courses
        $enrollments = Enrollment::with('course')
            ->where('user_id', $user->id)
            ->get();

        $this->enrolledCourses = $enrollments->map(function ($enrollment) {
            $course = $enrollment->course;
            if (! $course) {
                return null;
            }

            $contentIds = \App\Models\Content::whereHas('module.topic', fn ($q) => $q->where('course_id', $course->id))->pluck('id')->toArray();
            $totalModules = count($contentIds);

            $completedModules = Progress::where('user_id', $enrollment->user_id)
                ->whereIn('content_id', $contentIds)
                ->whereNotNull('completed_at')
                ->count();

            $progress = $totalModules > 0
                ? (int) round(($completedModules / $totalModules) * 100)
                : 0;

            $status = $progress === 100 ? 'completed' : ($progress > 0 ? 'in-progress' : 'not-started');

            return (object) [
                'id' => $course->id,
                'name' => $course->title ?? 'Unknown Course',
                'description' => $course->description,
                'modules' => $totalModules,
                'completedModules' => $completedModules,
                'progress' => $progress,
                'deadline' => $enrollment->deadline,
                'xpReward' => $enrollment->xp_reward ?? 500,
                'status' => $status,
            ];
        })->filter();
    }

    public function render()
    {
        return view('livewire.faculty.courses')->layout('layouts.app');
    }
}
