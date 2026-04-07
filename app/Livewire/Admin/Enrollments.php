<?php

namespace App\Livewire\Admin;

use App\Concerns\NormalizesEnrollmentDeadline;
use App\Models\Enrollment;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
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
                        })
                        ->orWhere('batch_id', 'like', '%'.$this->search.'%');
                });
            });

        /** @var Collection<int, Enrollment> $matchedEnrollments */
        $matchedEnrollments = $query
            ->orderByDesc('enrolled_at')
            ->get();

        $totalEnrollments = $matchedEnrollments->count();
        $activeLearners = $matchedEnrollments->pluck('user_id')->unique()->count();
        $activeCourses = $matchedEnrollments->pluck('course_id')->unique()->count();

        $batches = $matchedEnrollments
            ->groupBy(fn (Enrollment $enrollment): string => $this->batchKey($enrollment))
            ->map(function (Collection $batch): object {
                /** @var Enrollment $latestEnrollment */
                $latestEnrollment = $batch->sortByDesc('enrolled_at')->first();
                $deadlineMeta = $this->normalizeEnrollmentDeadline((int) $batch->max('deadline'));
                $learnerNames = $batch
                    ->pluck('user.name')
                    ->filter()
                    ->take(3)
                    ->implode(', ');

                return (object) [
                    'id' => $this->batchKey($latestEnrollment),
                    'batchId' => $latestEnrollment->batch_id ?: 'Legacy',
                    'course' => $latestEnrollment->course?->title ?? 'Unknown Course',
                    'courseUrl' => $latestEnrollment->course ? route('admin.courses.show', $latestEnrollment->course) : null,
                    'enrolledBy' => $latestEnrollment->enrolledBy?->name ?? 'Unknown User',
                    'enrolledAt' => $latestEnrollment->enrolled_at?->format('M d, Y'),
                    'learnersCount' => $batch->count(),
                    'learnersPreview' => $learnerNames,
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
                    'sortTimestamp' => $latestEnrollment->enrolled_at?->timestamp ?? 0,
                ];
            })
            ->sortByDesc('sortTimestamp')
            ->values();

        $totalBatches = $batches->count();
        $urgentBatches = $batches
            ->filter(fn (object $batch): bool => $batch->deadlineLabel !== 'No deadline' && str_contains($batch->deadlineTone, 'amber'))
            ->count();

        $currentPage = $this->getPage();
        $perPage = 10;
        $currentItems = $batches->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $enrollments = new LengthAwarePaginator(
            $currentItems,
            $totalBatches,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page'],
        );

        return view('livewire.admin.enrollments', [
            'activeCourses' => $activeCourses,
            'activeLearners' => $activeLearners,
            'enrollments' => $enrollments,
            'totalBatches' => $totalBatches,
            'totalEnrollments' => $totalEnrollments,
            'urgentBatches' => $urgentBatches,
        ])->layout('layouts.app');
    }

    protected function batchKey(Enrollment $enrollment): string
    {
        if ($enrollment->batch_id) {
            return $enrollment->batch_id;
        }

        return 'legacy-'.$enrollment->course_id.'-'.$enrollment->enrolled_by.'-'.($enrollment->enrolled_at?->timestamp ?? 0);
    }
}
