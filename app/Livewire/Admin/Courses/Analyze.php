<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Content;
use App\Models\Course;
use App\Models\Progress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class Analyze extends Component
{
    public Course $course;

    #[Url]
    public string $activeTab = 'overview';

    public array $stats = [];

    public array $enrollmentData = [];

    public array $progressData = [];

    public array $completionData = [];

    public array $moduleData = [];

    public function mount(Course $course): void
    {
        if (! Auth::user()->isAdmin()) {
            abort(403);
        }

        $this->course = Course::with(['topics.modules.contents', 'enrollments.user'])->findOrFail($course->id);
        $this->calculateStats();
    }

    protected function calculateStats(): void
    {
        $contents = $this->getContentsThroughTopics();
        $enrollments = $this->course->enrollments()->with('user')->get();

        $this->stats = [
            'totalEnrollments' => $enrollments->count(),
            'completedEnrollments' => 0,
            'inProgressEnrollments' => 0,
            'notStartedEnrollments' => 0,
            'avgProgress' => 0,
            'totalContent' => $contents->count(),
            'totalVideos' => $contents->where('type', 'video')->count(),
            'totalArticles' => $contents->where('type', 'article')->count(),
            'totalQuizzes' => $contents->where('type', 'quiz')->count(),
        ];

        $totalProgress = 0;
        $contentIds = $contents->pluck('id');

        $enrollmentProgress = [];
        foreach ($enrollments as $enrollment) {
            $completedContent = Progress::where('user_id', $enrollment->user_id)
                ->whereIn('content_id', $contentIds)
                ->whereNotNull('completed_at')
                ->count();

            $progress = $contentIds->count() > 0
                ? (int) round(($completedContent / $contentIds->count()) * 100)
                : 0;

            $enrollmentProgress[$enrollment->id] = $progress;
            $totalProgress += $progress;

            if ($progress === 100) {
                $this->stats['completedEnrollments']++;
            } elseif ($progress > 0) {
                $this->stats['inProgressEnrollments']++;
            } else {
                $this->stats['notStartedEnrollments']++;
            }
        }

        if ($enrollments->count() > 0) {
            $this->stats['avgProgress'] = (int) round($totalProgress / $enrollments->count());
        }

        $this->moduleData = $this->course->topics
            ->flatMap(function ($topic): array {
                return $topic->modules->map(function ($module) use ($topic): array {
                    $contentIds = $module->contents->pluck('id');
                    $totalContents = $contentIds->count();
                    $quizzes = $module->contents->where('type', 'quiz')->count();
                    $videos = $module->contents->where('type', 'video')->count();

                    $completedCount = 0;
                    $inProgressCount = 0;
                    foreach ($this->course->enrollments as $enrollment) {
                        $completed = Progress::where('user_id', $enrollment->user_id)
                            ->whereIn('content_id', $contentIds)
                            ->whereNotNull('completed_at')
                            ->count();

                        if ($totalContents > 0) {
                            $progress = ($completed / $totalContents) * 100;
                            if ($progress === 100) {
                                $completedCount++;
                            } elseif ($progress > 0) {
                                $inProgressCount++;
                            }
                        }
                    }

                    return [
                        'name' => $module->title,
                        'topic_name' => $topic->name,
                        'content_count' => $videos,
                        'quiz_count' => $quizzes,
                        'total_content' => $totalContents,
                        'completed_count' => $completedCount,
                        'in_progress_count' => $inProgressCount,
                        'not_started_count' => $this->course->enrollments->count() - $completedCount - $inProgressCount,
                    ];
                })->all();
            })
            ->all();

        $this->enrollmentData = $enrollments->map(function ($enrollment) use ($enrollmentProgress): array {
            return [
                'user_name' => $enrollment->user?->name ?? 'Unknown',
                'user_initial' => strtoupper(substr($enrollment->user?->name ?? '?', 0, 1)),
                'enrolled_at' => $enrollment->enrolled_at,
                'progress' => $enrollmentProgress[$enrollment->id] ?? 0,
            ];
        })->all();

        $this->calculateProgressDistribution($contentIds);
        $this->calculateCompletionTimeline($enrollments, $contentIds);
    }

    protected function getContentsThroughTopics(): Collection
    {
        $topicIds = $this->course->topics->pluck('id');

        return Content::whereIn('module_id', function ($query) use ($topicIds) {
            $query->select('id')
                ->from('modules')
                ->whereIn('topic_id', $topicIds);
        })->get();
    }

    protected function calculateProgressDistribution($contentIds): void
    {
        $distribution = [
            '0-25%' => 0,
            '26-50%' => 0,
            '51-75%' => 0,
            '76-99%' => 0,
            '100%' => 0,
        ];

        foreach ($this->course->enrollments as $enrollment) {
            $completedContent = Progress::where('user_id', $enrollment->user_id)
                ->whereIn('content_id', $contentIds)
                ->whereNotNull('completed_at')
                ->count();

            $progress = $contentIds->count() > 0
                ? (int) round(($completedContent / $contentIds->count()) * 100)
                : 0;

            if ($progress === 0) {
                $distribution['0-25%']++;
            } elseif ($progress <= 25) {
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

        $this->progressData = $distribution;
    }

    protected function calculateCompletionTimeline($enrollments, $contentIds): void
    {
        $timeline = [];

        foreach ($enrollments as $enrollment) {
            $completedAt = Progress::where('user_id', $enrollment->user_id)
                ->whereIn('content_id', $contentIds)
                ->whereNotNull('completed_at')
                ->orderBy('completed_at', 'desc')
                ->first();

            if ($completedAt) {
                $month = $completedAt->completed_at->format('Y-m');
                if (! isset($timeline[$month])) {
                    $timeline[$month] = 0;
                }
                $timeline[$month]++;
            }
        }

        $this->completionData = $timeline;
    }

    public function setTab(string $tab): void
    {
        if ($this->activeTab !== $tab) {
            $this->activeTab = $tab;
        }

        if ($tab === 'enrollments') {
            $this->dispatch('analyze-enrollments-tab-opened');
        }
    }

    public function updatedActiveTab(string $value): void
    {
        if ($value === 'enrollments') {
            $this->dispatch('analyze-enrollments-tab-opened');
        }
    }

    public function render(): View
    {
        return view('livewire.admin.courses.analyze');
    }
}
