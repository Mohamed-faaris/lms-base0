<?php

namespace App\Livewire\Faculty;

use App\Concerns\NormalizesEnrollmentDeadline;
use App\Models\Enrollment;
use App\Models\Progress;
use Illuminate\Support\Collection;
use Livewire\Component;

class Courses extends Component
{
    use NormalizesEnrollmentDeadline;

    public Collection $enrolledCourses;

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user) {
            $this->enrolledCourses = collect();

            return;
        }

        // Get enrolled courses with progress
        $this->enrolledCourses = Enrollment::with(['course.topics.modules.contents'])
            ->where('user_id', $user->id)
            ->get()
            ->map(function (Enrollment $enrollment) {
                $course = $enrollment->course;
                $contents = $course?->topics
                    ?->flatMap(fn ($topic) => $topic->modules)
                    ->flatMap(fn ($module) => $module->contents)
                    ?? collect();
                $totalModules = $contents->count();
                $completedModules = Progress::where('user_id', $enrollment->user_id)
                    ->whereIn('content_id', $contents->pluck('id'))
                    ->whereNotNull('completed_at')
                    ->count();

                $progress = $totalModules > 0
                    ? (int) round(($completedModules / $totalModules) * 100)
                    : 0;

                $status = $progress === 100 ? 'completed' : ($progress > 0 ? 'in-progress' : 'not-started');
                $deadlineMeta = $this->normalizeEnrollmentDeadline($enrollment->deadline);

                return (object) [
                    'id' => $course?->id,
                    'name' => $course?->title ?? 'Unknown Course',
                    'description' => $course?->description,
                    'modules' => $totalModules,
                    'completedModules' => $completedModules,
                    'progress' => $progress,
                    'deadline' => $enrollment->deadline,
                    'daysLeft' => $deadlineMeta['daysLeft'],
                    'hoursLeft' => $deadlineMeta['hoursLeft'],
                    'isUrgent' => $deadlineMeta['isUrgent'],
                    'isOverdue' => $deadlineMeta['isOverdue'],
                    'deadlineLabel' => $deadlineMeta['label'],
                    'deadlineCompactLabel' => $deadlineMeta['compactLabel'],
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
