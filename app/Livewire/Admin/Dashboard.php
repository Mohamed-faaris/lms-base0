<?php

namespace App\Livewire\Admin;

use App\Concerns\NormalizesEnrollmentDeadline;
use App\Enums\Role;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Feedback;
use App\Models\Progress;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class Dashboard extends Component
{
    use NormalizesEnrollmentDeadline;

    /** @var array<string, int> */
    public array $stats = [];

    /** @var list<array{label: string, value: int, max: int}> */
    public array $enrollmentTrend = [];

    /** @var list<array{label: string, value: int, max: int}> */
    public array $completionTrend = [];

    /** @var list<array<string, mixed>> */
    public array $urgentBatches = [];

    /** @var list<array<string, mixed>> */
    public array $recentBatches = [];

    /** @var list<array<string, mixed>> */
    public array $topEnrollmentCourses = [];

    /** @var list<array<string, mixed>> */
    public array $topCompletionCourses = [];

    /** @var list<array<string, mixed>> */
    public array $staleCourses = [];

    /** @var list<array<string, mixed>> */
    public array $recentFeedback = [];

    public function mount(): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $this->loadDashboard();
    }

    public function render(): View
    {
        return view('livewire.admin.dashboard')->layout('layouts.app');
    }

    protected function loadDashboard(): void
    {
        $this->loadStats();
        $this->loadTrends();
        $this->loadBatchInsights();
        $this->loadCourseInsights();
        $this->loadFeedback();
    }

    protected function loadStats(): void
    {
        $sevenDaysAgo = now()->subDays(6)->startOfDay();

        $this->stats = [
            'totalUsers' => User::count(),
            'facultyUsers' => User::query()->where('role', Role::Faculty)->count(),
            'totalCourses' => Course::count(),
            'totalEnrollments' => Enrollment::count(),
            'recentEnrollments' => Enrollment::query()
                ->where('enrolled_at', '>=', $sevenDaysAgo)
                ->count(),
            'recentCompletions' => Progress::query()
                ->where('completed_at', '>=', $sevenDaysAgo)
                ->count(),
        ];
    }

    protected function loadTrends(): void
    {
        $startDate = now()->subDays(6)->startOfDay();

        /** @var Collection<int, object{day: string, total: int}> $enrollmentRows */
        $enrollmentRows = Enrollment::query()
            ->selectRaw('DATE(enrolled_at) as day, COUNT(*) as total')
            ->where('enrolled_at', '>=', $startDate)
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        /** @var Collection<int, object{day: string, total: int}> $completionRows */
        $completionRows = Progress::query()
            ->selectRaw('DATE(completed_at) as day, COUNT(*) as total')
            ->where('completed_at', '>=', $startDate)
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $this->enrollmentTrend = $this->buildDailySeries($enrollmentRows, $startDate);
        $this->completionTrend = $this->buildDailySeries($completionRows, $startDate);
    }

    protected function loadBatchInsights(): void
    {
        /** @var Collection<int, Enrollment> $enrollments */
        $enrollments = Enrollment::query()
            ->with(['course', 'user', 'enrolledBy'])
            ->orderByDesc('enrolled_at')
            ->get();

        $batches = $enrollments
            ->groupBy(fn (Enrollment $enrollment): string => $this->batchKey($enrollment))
            ->map(function (Collection $batch): array {
                /** @var Enrollment $latestEnrollment */
                $latestEnrollment = $batch->sortByDesc('enrolled_at')->first();
                $deadlineValue = (int) $batch->max('deadline');
                $deadlineMeta = $this->normalizeEnrollmentDeadline($deadlineValue);

                return [
                    'id' => $this->batchKey($latestEnrollment),
                    'batchId' => $latestEnrollment->batch_id ?: 'Legacy',
                    'course' => $latestEnrollment->course?->title ?? 'Unknown Course',
                    'courseUrl' => $latestEnrollment->course ? route('admin.courses.show', $latestEnrollment->course) : null,
                    'enrollmentUrl' => route('admin.enrollments.show', $this->batchKey($latestEnrollment)),
                    'enrolledBy' => $latestEnrollment->enrolledBy?->name ?? 'Unknown User',
                    'enrolledAt' => $latestEnrollment->enrolled_at?->format('M d, Y'),
                    'learnersCount' => $batch->count(),
                    'deadlineLabel' => $deadlineMeta['label'],
                    'deadlineCompactLabel' => $deadlineMeta['compactLabel'],
                    'isUrgent' => $deadlineMeta['isUrgent'],
                    'isOverdue' => $deadlineMeta['isOverdue'],
                    'urgencyRank' => $deadlineMeta['isOverdue'] ? 0 : ($deadlineMeta['isUrgent'] ? 1 : 2),
                    'sortTimestamp' => $latestEnrollment->enrolled_at?->timestamp ?? 0,
                ];
            })
            ->values();

        $this->recentBatches = $batches
            ->sortByDesc('sortTimestamp')
            ->take(5)
            ->values()
            ->all();

        $this->urgentBatches = $batches
            ->filter(fn (array $batch): bool => $batch['isOverdue'] || $batch['isUrgent'])
            ->sortBy([
                ['urgencyRank', 'asc'],
                ['sortTimestamp', 'desc'],
            ])
            ->take(5)
            ->values()
            ->all();
    }

    protected function loadCourseInsights(): void
    {
        $staleThreshold = now()->subDays(14)->startOfDay();

        $this->topEnrollmentCourses = Course::query()
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->orderBy('title')
            ->limit(5)
            ->get()
            ->map(fn (Course $course): array => [
                'title' => $course->title,
                'enrollmentsCount' => $course->enrollments_count,
                'showUrl' => route('admin.courses.show', $course),
                'analyzeUrl' => route('admin.courses.analyze', $course),
            ])
            ->all();

        $this->topCompletionCourses = Course::query()
            ->leftJoin('topics', 'topics.course_id', '=', 'courses.id')
            ->leftJoin('modules', 'modules.topic_id', '=', 'topics.id')
            ->leftJoin('contents', 'contents.module_id', '=', 'modules.id')
            ->leftJoin('progress', 'progress.content_id', '=', 'contents.id')
            ->selectRaw('courses.id, courses.title, COUNT(progress.user_id) as completions_count')
            ->groupBy('courses.id', 'courses.title')
            ->orderByDesc('completions_count')
            ->orderBy('courses.title')
            ->limit(5)
            ->get()
            ->map(fn (object $course): array => [
                'title' => $course->title,
                'completionsCount' => (int) $course->completions_count,
                'showUrl' => route('admin.courses.show', $course->id),
            ])
            ->all();

        $this->staleCourses = Course::query()
            ->withCount('enrollments')
            ->leftJoin('topics', 'topics.course_id', '=', 'courses.id')
            ->leftJoin('modules', 'modules.topic_id', '=', 'topics.id')
            ->leftJoin('contents', 'contents.module_id', '=', 'modules.id')
            ->leftJoin('progress', function ($join) use ($staleThreshold): void {
                $join->on('progress.content_id', '=', 'contents.id')
                    ->where('progress.completed_at', '>=', $staleThreshold);
            })
            ->selectRaw('courses.id, courses.title, courses.slug, COUNT(DISTINCT enrollments.user_id) as enrollment_count, COUNT(progress.user_id) as recent_completion_count')
            ->join('enrollments', 'enrollments.course_id', '=', 'courses.id')
            ->groupBy('courses.id', 'courses.title', 'courses.slug')
            ->havingRaw('COUNT(progress.user_id) = 0')
            ->orderByDesc('enrollment_count')
            ->orderBy('courses.title')
            ->limit(5)
            ->get()
            ->map(fn (object $course): array => [
                'title' => $course->title,
                'enrollmentCount' => (int) $course->enrollment_count,
                'showUrl' => route('admin.courses.show', $course->id),
                'analyzeUrl' => route('admin.courses.analyze', $course->id),
            ])
            ->all();
    }

    protected function loadFeedback(): void
    {
        $this->recentFeedback = Feedback::query()
            ->with(['course', 'user'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn (Feedback $feedback): array => [
                'course' => $feedback->course?->title ?? 'Unknown Course',
                'courseUrl' => $feedback->course ? route('admin.courses.show', $feedback->course) : null,
                'user' => $feedback->user?->name ?? 'Unknown User',
                'rating' => $feedback->rating,
                'comment' => $feedback->comments ? str($feedback->comments)->limit(100)->value() : null,
                'createdAt' => $feedback->created_at?->format('M d, Y'),
            ])
            ->all();
    }

    /**
     * @param  Collection<int, object{day: string, total: int}>  $rows
     * @return list<array{label: string, value: int, max: int}>
     */
    protected function buildDailySeries(Collection $rows, CarbonInterface $startDate): array
    {
        /** @var array<string, int> $totalsByDate */
        $totalsByDate = $rows
            ->mapWithKeys(fn (object $row): array => [$row->day => (int) $row->total])
            ->all();

        $series = collect(range(0, 6))
            ->map(function (int $offset) use ($startDate, $totalsByDate): array {
                $date = $startDate->copy()->addDays($offset);
                $key = $date->toDateString();

                return [
                    'label' => $date->format('D'),
                    'value' => $totalsByDate[$key] ?? 0,
                    'max' => 0,
                ];
            })
            ->values();

        $max = max(1, $series->max('value'));

        return $series
            ->map(fn (array $day): array => [
                ...$day,
                'max' => $max,
            ])
            ->all();
    }

    protected function batchKey(Enrollment $enrollment): string
    {
        if ($enrollment->batch_id) {
            return (string) $enrollment->batch_id;
        }

        return 'legacy-'.$enrollment->course_id.'-'.$enrollment->enrolled_by.'-'.($enrollment->enrolled_at?->timestamp ?? 0);
    }
}
