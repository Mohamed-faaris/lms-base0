<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Content;
use App\Models\Course;
use App\Models\Progress;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Analyze extends Component
{
    public Course $course;

    public string $activeTab = 'overview';

    public array $stats = [];

    public array $enrollmentData = [];

    public array $progressData = [];

    public array $completionData = [];

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

        $this->enrollmentData = $enrollments->map(function ($e) use ($enrollmentProgress) {
            return [
                'user' => $e->user,
                'enrolled_at' => $e->enrolled_at,
                'progress' => $enrollmentProgress[$e->id] ?? 0,
            ];
        })->toArray();

        $this->calculateProgressDistribution($contentIds);
        $this->calculateCompletionTimeline($enrollments, $contentIds);
    }

    protected function getContentsThroughTopics()
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
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.admin.courses.analyze');
    }
}
