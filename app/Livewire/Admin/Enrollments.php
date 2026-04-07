<?php

namespace App\Livewire\Admin;

use App\Concerns\NormalizesEnrollmentDeadline;
use App\Models\Enrollment;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class Enrollments extends Component
{
    use NormalizesEnrollmentDeadline;
    use WithPagination;

    public string $search = '';

    public function mount(): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Enrollment::query()
            ->with(['course', 'user', 'enrolledBy'])
            ->when($this->search !== '', function ($builder): void {
                $builder->where(function ($searchQuery): void {
                    $searchQuery
                        ->whereHas('user', function ($userQuery): void {
                            $userQuery
                                ->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('email', 'like', '%'.$this->search.'%');
                        })
                        ->orWhereHas('course', function ($courseQuery): void {
                            $courseQuery
                                ->where('title', 'like', '%'.$this->search.'%')
                                ->orWhere('slug', 'like', '%'.$this->search.'%');
                        });
                });
            });

        $totalEnrollments = (clone $query)->count();
        $activeLearners = (clone $query)->distinct('user_id')->count('user_id');
        $activeCourses = (clone $query)->distinct('course_id')->count('course_id');
        $urgentDeadlines = (clone $query)
            ->where('deadline', '>=', 1_000_000_000)
            ->whereBetween('deadline', [now()->timestamp, now()->addDays(3)->timestamp])
            ->count();

        /** @var LengthAwarePaginator<int, Enrollment> $enrollments */
        $enrollments = $query
            ->orderByDesc('enrolled_at')
            ->paginate(10)
            ->through(function (Enrollment $enrollment): object {
                $deadlineMeta = $this->normalizeEnrollmentDeadline($enrollment->deadline);

                return (object) [
                    'id' => $enrollment->course_id.'-'.$enrollment->user_id,
                    'course' => $enrollment->course?->title ?? 'Unknown Course',
                    'courseUrl' => $enrollment->course ? route('admin.courses.show', $enrollment->course) : null,
                    'learner' => $enrollment->user?->name ?? 'Unknown User',
                    'learnerEmail' => $enrollment->user?->email ?? 'No email',
                    'enrolledBy' => $enrollment->enrolledBy?->name ?? 'Unknown User',
                    'enrolledAt' => $enrollment->enrolled_at?->format('M d, Y'),
                    'deadlineLabel' => match (true) {
                        $deadlineMeta['isOverdue'] => 'Overdue',
                        $deadlineMeta['daysLeft'] === null => 'No deadline',
                        default => $deadlineMeta['daysLeft'].' days left',
                    },
                    'deadlineTone' => match (true) {
                        $deadlineMeta['isOverdue'] => 'text-red-600 dark:text-red-400',
                        $deadlineMeta['isUrgent'] => 'text-amber-600 dark:text-amber-400',
                        default => 'text-zinc-600 dark:text-zinc-300',
                    },
                ];
            });

        return view('livewire.admin.enrollments', [
            'activeCourses' => $activeCourses,
            'activeLearners' => $activeLearners,
            'enrollments' => $enrollments,
            'totalEnrollments' => $totalEnrollments,
            'urgentDeadlines' => $urgentDeadlines,
        ])->layout('layouts.app');
    }
}
