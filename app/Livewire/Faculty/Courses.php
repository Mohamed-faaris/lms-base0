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

        // Get enrolled courses with progress
        $this->enrolledCourses = Enrollment::with(['course.contents'])
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($enrollment) {
                $course = $enrollment->course;
                $contents = $course?->contents ?? collect();
                $totalModules = $contents->count();
                $completedModules = Progress::where('user_id', $enrollment->user_id)
                    ->whereIn('content_id', $contents->pluck('id'))
                    ->whereNotNull('completed_at')
                    ->count();

                $progress = $totalModules > 0
                    ? (int) round(($completedModules / $totalModules) * 100)
                    : 0;

                $status = $progress === 100 ? 'completed' : ($progress > 0 ? 'in-progress' : 'not-started');

                return (object) [
                    'id' => $course?->id,
                    'name' => $course?->title ?? 'Unknown Course',
                    'description' => $course?->description,
                    'modules' => $totalModules,
                    'completedModules' => $completedModules,
                    'progress' => $progress,
                    'deadline' => $enrollment->deadline,
                    'xpReward' => $enrollment->xp_reward ?? 500,
                    'status' => $status,
                ];
            });
    }

    public function render()
    {
        return view('livewire.faculty.courses')->layout('layouts.app');
    }
}
