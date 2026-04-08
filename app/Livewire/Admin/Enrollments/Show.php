<?php

namespace App\Livewire\Admin\Enrollments;

use App\Concerns\NormalizesEnrollmentDeadline;
use App\Models\Content;
use App\Models\Enrollment;
use App\Models\Progress;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

class Show extends Component
{
    use NormalizesEnrollmentDeadline;

    public string $batchKey;

    public string $learnerSearch = '';

    public string $progressFilter = 'all';

    public string $sortBy = 'name';

    public string $sortDirection = 'asc';

    public bool $showRevokeBatchModal = false;

    public bool $showLearnerDeadlineModal = false;

    public string $revokeBatchConfirmation = '';

    public ?int $selectedLearnerId = null;

    public string $selectedLearnerName = '';

    public string $selectedLearnerDeadlineDays = '1';

    /** @var array<int, string> */
    public array $learnerDeadlineDays = [];

    public function mount(string $batchKey): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $this->batchKey = $batchKey;
        $this->syncEditableFields();
    }

    public function openRevokeBatchModal(): void
    {
        $this->resetValidation('revokeBatchConfirmation');
        $this->revokeBatchConfirmation = '';
        $this->showRevokeBatchModal = true;
    }

    public function closeRevokeBatchModal(): void
    {
        $this->resetValidation('revokeBatchConfirmation');
        $this->revokeBatchConfirmation = '';
        $this->showRevokeBatchModal = false;
    }

    public function openLearnerDeadlineModal(int $userId): void
    {
        $enrollment = $this->batchQuery()
            ->with('user')
            ->where('user_id', $userId)
            ->firstOrFail();

        $this->resetValidation('selectedLearnerDeadlineDays');
        $this->selectedLearnerId = $userId;
        $this->selectedLearnerName = $enrollment->user?->name ?? 'this learner';
        $this->selectedLearnerDeadlineDays = $this->learnerDeadlineDays[$userId]
            ?? (string) max(1, $this->normalizeDeadlineDayDifference((int) $enrollment->deadline - now()->timestamp));
        $this->showLearnerDeadlineModal = true;
    }

    public function closeLearnerDeadlineModal(): void
    {
        $this->resetValidation('selectedLearnerDeadlineDays');
        $this->selectedLearnerId = null;
        $this->selectedLearnerName = '';
        $this->selectedLearnerDeadlineDays = '1';
        $this->showLearnerDeadlineModal = false;
    }

    public function setSort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortBy = $field;
        $this->sortDirection = 'asc';
    }

    public function setProgressFilter(string $filter): void
    {
        $this->progressFilter = $filter;
    }

    public function saveLearnerDeadline(int $userId): Redirector
    {
        $validated = $this->validate([
            "learnerDeadlineDays.{$userId}" => ['required', 'integer', 'min:1'],
        ]);

        $days = (int) $validated['learnerDeadlineDays'][$userId];

        $this->batchQuery()
            ->where('user_id', $userId)
            ->update([
                'deadline' => now()->addDays($days)->timestamp,
            ]);

        session()->flash('success', 'Learner deadline updated successfully.');

        return redirect()->route('admin.enrollments.show', $this->batchKey);
    }

    public function saveSelectedLearnerDeadline(): Redirector
    {
        if ($this->selectedLearnerId === null) {
            abort(404);
        }

        $validated = $this->validate([
            'selectedLearnerDeadlineDays' => ['required', 'integer', 'min:1'],
        ]);

        $days = (int) $validated['selectedLearnerDeadlineDays'];

        $this->batchQuery()
            ->where('user_id', $this->selectedLearnerId)
            ->update([
                'deadline' => now()->addDays($days)->timestamp,
            ]);

        session()->flash('success', 'Learner deadline updated successfully.');

        return redirect()->route('admin.enrollments.show', $this->batchKey);
    }

    public function revokeLearner(int $userId): Redirector
    {
        $this->batchQuery()
            ->where('user_id', $userId)
            ->delete();

        if ($this->resolveBatchEnrollments()->isEmpty()) {
            session()->flash('success', 'Learner removed from the batch.');

            return redirect()->route('admin.enrollments.index');
        }

        $this->syncEditableFields();
        session()->flash('success', 'Learner removed from the batch.');

        return redirect()->route('admin.enrollments.show', $this->batchKey);
    }

    public function revokeBatch(): Redirector
    {
        $this->validate([
            'revokeBatchConfirmation' => ['required', 'in:CONFIRM'],
        ], [
            'revokeBatchConfirmation.required' => 'Type CONFIRM to revoke this batch.',
            'revokeBatchConfirmation.in' => 'Type CONFIRM exactly to revoke this batch.',
        ]);

        $this->batchQuery()->delete();

        session()->flash('success', 'Batch revoked successfully.');

        return redirect()->route('admin.enrollments.index');
    }

    public function render(): View
    {
        $enrollments = $this->resolveBatchEnrollments();

        if ($enrollments->isEmpty()) {
            abort(404);
        }

        $batchSummary = $this->batchSummary($enrollments);
        $progressByUser = $this->progressByUser($enrollments);
        $filteredEnrollments = $this->filteredLearnerEnrollments($enrollments, $progressByUser);

        return view('livewire.admin.enrollments.show', [
            'batch' => $batchSummary,
            'learners' => $this->learnerRows($filteredEnrollments, $progressByUser),
            'learnersCount' => $enrollments->count(),
            'progressDistribution' => $this->progressDistribution($enrollments, $progressByUser),
        ])->layout('layouts.app');
    }

    protected function syncEditableFields(): void
    {
        $enrollments = $this->resolveBatchEnrollments();

        if ($enrollments->isEmpty()) {
            return;
        }

        $this->syncLearnerDeadlineFields($enrollments);
    }

    protected function resolveBatchEnrollments(): Collection
    {
        return $this->batchQuery()
            ->with(['course.topics.modules.contents', 'user', 'enrolledBy'])
            ->orderBy('enrolled_at')
            ->get();
    }

    protected function filteredLearnerEnrollments(Collection $enrollments, array $progressByUser): Collection
    {
        $filteredEnrollments = $enrollments;

        if ($this->learnerSearch !== '') {
            $search = mb_strtolower($this->learnerSearch);

            $filteredEnrollments = $filteredEnrollments->filter(function (Enrollment $enrollment) use ($search): bool {
                return str_contains(mb_strtolower((string) ($enrollment->user?->name ?? '')), $search)
                    || str_contains(mb_strtolower((string) ($enrollment->user?->email ?? '')), $search);
            });
        }

        if ($this->progressFilter !== 'all') {
            $filteredEnrollments = $filteredEnrollments->filter(function (Enrollment $enrollment) use ($progressByUser): bool {
                $progress = $progressByUser[$enrollment->user_id] ?? 0;

                return match ($this->progressFilter) {
                    'not-started' => $progress === 0,
                    '25' => $progress >= 25 && $progress < 50,
                    '50' => $progress >= 50 && $progress < 75,
                    '75' => $progress >= 75 && $progress < 100,
                    '100' => $progress === 100,
                    default => true,
                };
            });
        }

        return $filteredEnrollments
            ->sortBy(function (Enrollment $enrollment) use ($progressByUser): int|string {
                return match ($this->sortBy) {
                    'progress' => $progressByUser[$enrollment->user_id] ?? 0,
                    'deadline' => (int) $enrollment->deadline,
                    default => mb_strtolower((string) ($enrollment->user?->name ?? '')),
                };
            }, options: SORT_NATURAL, descending: $this->sortDirection === 'desc')
            ->values();
    }

    protected function batchQuery(): Builder
    {
        if (str_starts_with($this->batchKey, 'legacy-')) {
            [, $courseId, $enrolledBy, $enrolledAt] = array_pad(explode('-', $this->batchKey, 4), 4, null);

            if (! is_numeric($courseId) || ! is_numeric($enrolledBy) || ! is_numeric($enrolledAt)) {
                abort(404);
            }

            return Enrollment::query()
                ->where('course_id', (int) $courseId)
                ->where('enrolled_by', (int) $enrolledBy)
                ->where('enrolled_at', Carbon::createFromTimestamp((int) $enrolledAt)->toDateTimeString());
        }

        return Enrollment::query()->where('batch_id', $this->batchKey);
    }

    protected function batchSummary(Collection $enrollments): array
    {
        /** @var Enrollment $firstEnrollment */
        $firstEnrollment = $enrollments->first();
        $deadlineMeta = $this->normalizeEnrollmentDeadline((int) $enrollments->max('deadline'));
        $batchId = $firstEnrollment->batch_id !== null ? (string) $firstEnrollment->batch_id : null;
        $course = $firstEnrollment->course;

        return [
            'batchId' => $batchId,
            'displayBatchId' => $batchId ?? 'Legacy batch',
            'batchKey' => $this->batchKey,
            'courseId' => $course?->id,
            'courseTitle' => $course?->title ?? 'Unknown Course',
            'courseUrl' => $course ? route('admin.courses.show', $course) : null,
            'enrolledBy' => $firstEnrollment->enrolledBy?->name ?? 'Unknown User',
            'enrolledAt' => $firstEnrollment->enrolled_at?->format('M d, Y'),
            'learnersCount' => $enrollments->count(),
            'deadlineLabel' => $deadlineMeta['label'],
            'deadlineTone' => match (true) {
                $deadlineMeta['isOverdue'] => 'text-red-600 dark:text-red-400',
                $deadlineMeta['isUrgent'] => 'text-amber-600 dark:text-amber-400',
                default => 'text-zinc-600 dark:text-zinc-300',
            },
            'isLegacy' => $batchId === null,
        ];
    }

    protected function learnerRows(Collection $enrollments, array $progressByUser): array
    {
        return $enrollments->map(function (Enrollment $enrollment) use ($progressByUser): array {
            $deadlineMeta = $this->normalizeEnrollmentDeadline((int) $enrollment->deadline);
            $progress = $progressByUser[$enrollment->user_id] ?? 0;

            return [
                'userId' => $enrollment->user_id,
                'name' => $enrollment->user?->name ?? 'Unknown User',
                'email' => $enrollment->user?->email ?? 'No email',
                'enrolledBy' => $enrollment->enrolledBy?->name ?? 'Unknown User',
                'enrolledAt' => $enrollment->enrolled_at?->format('M d, Y'),
                'deadlineLabel' => $deadlineMeta['label'],
                'deadlineTone' => match (true) {
                    $deadlineMeta['isOverdue'] => 'text-red-600 dark:text-red-400',
                    $deadlineMeta['isUrgent'] => 'text-amber-600 dark:text-amber-400',
                    default => 'text-zinc-600 dark:text-zinc-300',
                },
                'progress' => $progress,
                'progressStatus' => match (true) {
                    $progress === 100 => 'Completed',
                    $progress > 0 => 'In Progress',
                    default => 'Not Started',
                },
                'progressBadgeColor' => match (true) {
                    $progress === 100 => 'green',
                    $progress > 0 => 'yellow',
                    default => 'zinc',
                },
                'statusLabel' => match (true) {
                    $progress === 100 => '100%',
                    $progress >= 75 => '75%',
                    $progress >= 50 => '50%',
                    $progress >= 25 => '25%',
                    default => 'Not Started',
                },
                'statusBadgeColor' => match (true) {
                    $progress === 100 => 'green',
                    $progress >= 75 => 'emerald',
                    $progress >= 50 => 'blue',
                    $progress >= 25 => 'amber',
                    default => 'zinc',
                },
                'deadlineDaysInput' => $this->learnerDeadlineDays[$enrollment->user_id]
                    ?? (string) max(1, $this->normalizeDeadlineDayDifference((int) $enrollment->deadline - now()->timestamp)),
            ];
        })->all();
    }

    protected function progressByUser(Collection $enrollments): array
    {
        /** @var Enrollment|null $firstEnrollment */
        $firstEnrollment = $enrollments->first();
        $courseId = $firstEnrollment?->course_id;

        if ($courseId === null) {
            return $enrollments
                ->mapWithKeys(fn (Enrollment $enrollment): array => [$enrollment->user_id => 0])
                ->all();
        }

        $contentIds = Content::query()
            ->whereHas('module.topic', function (Builder $query) use ($courseId): void {
                $query->where('course_id', $courseId);
            })
            ->pluck('id')
            ->values();

        if ($contentIds->isEmpty()) {
            return $enrollments
                ->mapWithKeys(fn (Enrollment $enrollment): array => [$enrollment->user_id => 0])
                ->all();
        }

        $completedCounts = Progress::query()
            ->whereIn('user_id', $enrollments->pluck('user_id'))
            ->whereIn('content_id', $contentIds)
            ->whereNotNull('completed_at')
            ->selectRaw('user_id, count(*) as completed_count')
            ->groupBy('user_id')
            ->pluck('completed_count', 'user_id');

        $totalContent = $contentIds->count();

        return $enrollments
            ->mapWithKeys(function (Enrollment $enrollment) use ($completedCounts, $totalContent): array {
                $completedContent = (int) ($completedCounts[$enrollment->user_id] ?? 0);

                return [
                    $enrollment->user_id => (int) round(($completedContent / $totalContent) * 100),
                ];
            })
            ->all();
    }

    protected function progressDistribution(Collection $enrollments, array $progressByUser): array
    {
        $distribution = [
            '0-25%' => 0,
            '26-50%' => 0,
            '51-75%' => 0,
            '76-99%' => 0,
            '100%' => 0,
        ];

        foreach ($enrollments as $enrollment) {
            $progress = $progressByUser[$enrollment->user_id] ?? 0;

            if ($progress <= 25) {
                $distribution['0-25%']++;
            } elseif ($progress <= 50) {
                $distribution['26-50%']++;
            } elseif ($progress <= 75) {
                $distribution['51-75%']++;
            } elseif ($progress < 100) {
                $distribution['76-99%']++;
            } else {
                $distribution['100%']++;
            }
        }

        return $distribution;
    }

    protected function syncLearnerDeadlineFields(Collection $enrollments): void
    {
        $this->learnerDeadlineDays = $enrollments
            ->mapWithKeys(function (Enrollment $enrollment): array {
                $deadline = (int) $enrollment->deadline;

                return [
                    $enrollment->user_id => (string) max(1, $deadline > 0
                        ? $this->normalizeDeadlineDayDifference($deadline - now()->timestamp)
                        : 1),
                ];
            })
            ->all();
    }
}
