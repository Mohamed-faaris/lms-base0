<?php

namespace App\Livewire\Admin\Enrollments;

use App\Concerns\NormalizesEnrollmentDeadline;
use App\Models\Enrollment;
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

    public string $batchIdInput = '';

    public string $deadlineDays = '1';

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

    public function saveBatchChanges(): Redirector
    {
        $validated = $this->validate([
            'batchIdInput' => ['nullable', 'string', 'max:255'],
            'deadlineDays' => ['required', 'integer', 'min:1'],
        ]);

        $enrollments = $this->resolveBatchEnrollments();

        if ($enrollments->isEmpty()) {
            abort(404);
        }

        $currentBatchId = $this->currentBatchId($enrollments);
        $newBatchId = trim((string) $validated['batchIdInput']);

        if ($newBatchId === '' && $currentBatchId !== null) {
            $newBatchId = $currentBatchId;
        }

        $updates = [
            'deadline' => now()->addDays((int) $validated['deadlineDays'])->timestamp,
        ];

        if ($newBatchId !== '') {
            $updates['batch_id'] = $newBatchId;
        }

        $this->batchQuery()->update($updates);

        if ($currentBatchId !== $newBatchId && $newBatchId !== '') {
            session()->flash('success', 'Batch updated successfully.');

            return redirect()->route('admin.enrollments.show', $newBatchId);
        }

        $this->syncEditableFields();
        session()->flash('success', 'Batch updated successfully.');

        return redirect()->route('admin.enrollments.show', $this->batchKey);
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
        $filteredEnrollments = $this->filteredLearnerEnrollments($enrollments);

        return view('livewire.admin.enrollments.show', [
            'batch' => $batchSummary,
            'learners' => $this->learnerRows($filteredEnrollments),
            'learnersCount' => $enrollments->count(),
        ])->layout('layouts.app');
    }

    protected function syncEditableFields(): void
    {
        $enrollments = $this->resolveBatchEnrollments();

        if ($enrollments->isEmpty()) {
            return;
        }

        $this->batchIdInput = $this->currentBatchId($enrollments) ?? '';
        $this->deadlineDays = (string) $this->currentDeadlineDays($enrollments);
        $this->syncLearnerDeadlineFields($enrollments);
    }

    protected function resolveBatchEnrollments(): Collection
    {
        return $this->batchQuery()
            ->with(['course', 'user', 'enrolledBy'])
            ->orderBy('enrolled_at')
            ->get();
    }

    protected function filteredLearnerEnrollments(Collection $enrollments): Collection
    {
        if ($this->learnerSearch === '') {
            return $enrollments;
        }

        $search = mb_strtolower($this->learnerSearch);

        return $enrollments->filter(function (Enrollment $enrollment) use ($search): bool {
            return str_contains(mb_strtolower((string) ($enrollment->user?->name ?? '')), $search)
                || str_contains(mb_strtolower((string) ($enrollment->user?->email ?? '')), $search);
        })->values();
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

    protected function learnerRows(Collection $enrollments): array
    {
        return $enrollments->map(function (Enrollment $enrollment): array {
            $deadlineMeta = $this->normalizeEnrollmentDeadline((int) $enrollment->deadline);

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
                'deadlineDaysInput' => $this->learnerDeadlineDays[$enrollment->user_id]
                    ?? (string) max(1, (int) ceil(((int) $enrollment->deadline - now()->timestamp) / 86400)),
            ];
        })->all();
    }

    protected function syncLearnerDeadlineFields(Collection $enrollments): void
    {
        $this->learnerDeadlineDays = $enrollments
            ->mapWithKeys(function (Enrollment $enrollment): array {
                $deadline = (int) $enrollment->deadline;

                return [
                    $enrollment->user_id => (string) max(1, $deadline > 0
                        ? (int) ceil(($deadline - now()->timestamp) / 86400)
                        : 1),
                ];
            })
            ->all();
    }

    protected function currentBatchId(Collection $enrollments): ?string
    {
        /** @var Enrollment $firstEnrollment */
        $firstEnrollment = $enrollments->first();

        return $firstEnrollment->batch_id !== null ? (string) $firstEnrollment->batch_id : null;
    }

    protected function currentDeadlineDays(Collection $enrollments): int
    {
        $deadline = (int) $enrollments->max('deadline');

        if ($deadline < 1) {
            return 1;
        }

        $daysRemaining = (int) ceil(($deadline - now()->timestamp) / 86400);

        return max(1, $daysRemaining);
    }
}
