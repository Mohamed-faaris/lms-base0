<?php

namespace App\Livewire\Manager\Faculty;

use App\Concerns\NormalizesEnrollmentDeadline;
use App\Models\Progress;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Profile extends Component
{
    use NormalizesEnrollmentDeadline;

    public User $user;

    public function mount(User $user): void
    {
        abort_unless(auth()->user()?->isManager(), 403);
        abort_unless(auth()->user()->canMonitorFaculty($user), 403);

        $this->user = $user;
    }

    public function render(): View
    {
        $enrollments = $this->user->enrollments()->with(['course.topics.modules.contents'])->get();
        $enrollmentData = [];

        foreach ($enrollments as $enrollment) {
            $contents = $enrollment->course->topics->flatMap->modules->flatMap->contents;
            $totalContent = $contents->count();

            $completedContent = Progress::query()
                ->where('user_id', $this->user->id)
                ->whereIn('content_id', $contents->pluck('id'))
                ->whereNotNull('completed_at')
                ->count();

            $progress = $totalContent > 0 ? (int) round(($completedContent / $totalContent) * 100) : 0;
            $deadlineMeta = $this->normalizeEnrollmentDeadline((int) $enrollment->deadline);

            $enrollmentData[] = (object) [
                'course' => $enrollment->course->title,
                'progress' => $progress,
                'enrolledAt' => $enrollment->enrolled_at?->format('M d, Y') ?? 'Unknown',
                'deadlineLabel' => $deadlineMeta['label'],
                'deadlineTone' => match (true) {
                    $deadlineMeta['isOverdue'] => 'text-red-600 dark:text-red-400',
                    $deadlineMeta['isUrgent'] => 'text-amber-600 dark:text-amber-400',
                    default => 'text-zinc-600 dark:text-zinc-300',
                },
            ];
        }

        return view('livewire.manager.faculty.profile', [
            'enrollments' => $enrollmentData,
        ])->layout('layouts.app');
    }
}
