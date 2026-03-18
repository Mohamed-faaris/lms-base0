<?php

namespace App\Livewire\Faculty;

use App\Models\Badge;
use App\Models\EndQuiz;
use App\Models\Enrollment;
use App\Models\Progress;
use App\Models\QuizAttempt;
use App\Models\Streak;
use App\Models\Xp;
use Illuminate\Support\Collection;
use Livewire\Component;

class Dashboard extends Component
{
    public Collection $enrolledCourses;

    public int $totalXp = 0;

    public int $level = 1;

    public int $streak = 0;

    public int $weeklyStreak = 0;

    public ?Badge $currentBadge = null;

    public ?Badge $nextBadge = null;

    public int $xpToNextLevel = 500;

    public array $weekDays = [];

    public float $averageScore = 0;

    public array $scoreTrend = [];

    public array $courseProgress = [];

    public array $leaderboard = [];

    public int $userRank = 0;

    public function mount(): void
    {
        $this->loadUserData();
        $this->loadWeekDays();
        $this->loadPerformanceData();
        $this->loadCourseProgress();
        $this->loadLeaderboard();
    }

    protected function loadUserData(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        // Get XP
        $xpRecord = Xp::find($user->id);
        $this->totalXp = $xpRecord?->xp ?? 0;
        $this->level = (int) floor($this->totalXp / 500) + 1;

        // Calculate XP to next level
        $nextLevelXp = $this->level * 500;
        $this->xpToNextLevel = $nextLevelXp - $this->totalXp;

        // Get Streak
        $streakRecord = Streak::where('user_id', $user->id)->orderBy('date', 'desc')->first();
        $this->streak = $streakRecord?->count ?? 0;

        // Calculate weekly streak (how many days completed this week)
        $weekStart = now()->startOfWeek();
        $weeklyStreaks = Streak::where('user_id', $user->id)
            ->where('date', '>=', $weekStart)
            ->count();
        $this->weeklyStreak = min($weeklyStreaks, 7);

        // Get badges efficiently with SQL ordering
        $badges = Badge::where('conditions', '!=', '[]')
            ->orderByRaw("CAST(conditions->>'$.min_xp' AS UNSIGNED) ASC")
            ->get();

        $this->currentBadge = $badges->first(fn ($b) => ($b->conditions['min_xp'] ?? 0) <= $this->totalXp);
        $this->nextBadge = $badges->first(fn ($b) => ($b->conditions['min_xp'] ?? 0) > $this->totalXp);

        // Get enrolled courses with progress - eager load everything needed
        $enrollments = Enrollment::with(['course.contents'])
            ->where('user_id', $user->id)
            ->get();

        // Pre-fetch all progress records for these enrollments in one query
        $allContentIds = $enrollments->flatMap(fn ($e) => $e->course?->contents ?? collect())->pluck('id')->unique()->toArray();

        $progressCounts = [];
        if (! empty($allContentIds)) {
            $progressCounts = Progress::where('user_id', $user->id)
                ->whereIn('content_id', $allContentIds)
                ->whereNotNull('completed_at')
                ->get()
                ->groupBy(fn ($p) => $p->content_id)
                ->map(fn ($group) => $group->count())
                ->toArray();
        }

        $this->enrolledCourses = $enrollments->map(function ($enrollment) use ($progressCounts) {
            $course = $enrollment->course;
            $contents = $course?->contents ?? collect();
            $totalModules = $contents->count();

            $completedModules = $contents->reduce(fn ($carry, $content) => $carry + ($progressCounts[$content->id] ?? 0), 0);

            $progress = $totalModules > 0
                ? (int) round(($completedModules / $totalModules) * 100)
                : 0;

            $status = $progress === 100 ? 'completed' : ($progress > 0 ? 'in-progress' : 'not-started');

            return (object) [
                'id' => $course?->id,
                'name' => $course?->title ?? 'Unknown Course',
                'modules' => $totalModules,
                'completedModules' => $completedModules,
                'progress' => $progress,
                'deadline' => $enrollment->deadline,
                'xpReward' => $enrollment->xp_reward ?? 500,
                'status' => $status,
            ];
        });
    }

    protected function loadWeekDays(): void
    {
        $weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $currentDay = now()->dayOfWeek;

        $this->weekDays = array_map(function ($day, $index) use ($currentDay) {
            return [
                'name' => $day,
                'isToday' => ($index + 1) === $currentDay,
                'isCompleted' => false,
            ];
        }, $weekDays, array_keys($weekDays));
    }

    protected function loadPerformanceData(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $attempts = QuizAttempt::where('user_id', $user->id)->get();

        $this->averageScore = $attempts->avg('score') ?? 0;

        $trendData = QuizAttempt::where('user_id', $user->id)
            ->selectRaw('DATE(attempted_at) as date, AVG(score) as avg_score')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get()
            ->reverse()
            ->values();

        $this->scoreTrend = $trendData->map(fn ($item) => [
            'date' => \Carbon\Carbon::parse($item->date)->format('M d'),
            'score' => (int) $item->avg_score,
        ])->toArray();
    }

    protected function loadCourseProgress(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        // Eager load course.contents for all enrollments
        $enrollments = Enrollment::with(['course.contents'])
            ->where('user_id', $user->id)
            ->get();

        // Pre-fetch all content IDs and progress in bulk
        $allContentIds = $enrollments->flatMap(fn ($e) => $e->course?->contents ?? collect())->pluck('id')->unique()->toArray();

        $progressCounts = [];
        if (! empty($allContentIds)) {
            $progressCounts = Progress::where('user_id', $user->id)
                ->whereIn('content_id', $allContentIds)
                ->whereNotNull('completed_at')
                ->get()
                ->groupBy(fn ($p) => $p->content_id)
                ->map(fn ($group) => $group->count())
                ->toArray();
        }

        // Pre-fetch quiz attempts for all content
        $allQuizIds = [];
        foreach ($enrollments as $enrollment) {
            $contents = $enrollment->course?->contents ?? collect();
            $quizIds = EndQuiz::whereIn('content_id', $contents->pluck('id'))->pluck('id')->toArray();
            $allQuizIds = array_merge($allQuizIds, $quizIds);
        }

        $quizScores = [];
        if (! empty($allQuizIds)) {
            $quizScores = QuizAttempt::where('user_id', $user->id)
                ->whereIn('quiz_id', $allQuizIds)
                ->get()
                ->groupBy(fn ($a) => $a->quiz_id)
                ->map(fn ($group) => $group->avg('score'))
                ->toArray();
        }

        // Pre-fetch EndQuiz content_id mapping
        $quizContentMap = EndQuiz::whereIn('id', $allQuizIds ?? [])->pluck('content_id', 'id')->toArray();

        $this->courseProgress = $enrollments->map(function ($enrollment) use ($progressCounts, $quizScores, $quizContentMap) {
            $course = $enrollment->course;
            $contents = $course?->contents ?? collect();
            $totalContents = $contents->count();

            $completedContents = $contents->reduce(fn ($carry, $content) => $carry + ($progressCounts[$content->id] ?? 0), 0);

            $progressPercent = $totalContents > 0
                ? (int) round(($completedContents / $totalContents) * 100)
                : 0;

            // Calculate average score from pre-fetched data
            $contentIds = $contents->pluck('id')->toArray();
            $relevantQuizIds = array_keys(array_intersect($quizContentMap, $contentIds));
            $scores = array_intersect_key($quizScores, array_flip($relevantQuizIds));
            $avgScore = ! empty($scores) ? array_sum($scores) / count($scores) : 0;

            return [
                'id' => $course?->id,
                'name' => $course?->title ?? 'Unknown Course',
                'totalModules' => $totalContents,
                'completedModules' => $completedContents,
                'progress' => $progressPercent,
                'avgScore' => (int) $avgScore,
                'isCompleted' => $progressPercent === 100,
            ];
        })->toArray();
    }

    protected function loadLeaderboard(): void
    {
        // Get all faculty with their XP records in one query
        $faculties = \App\Models\User::where('role', 'faculty')
            ->with('enrollments')
            ->get();

        // Pre-fetch all XP records for faculty in one query
        $facultyIds = $faculties->pluck('id')->toArray();
        $xpRecords = Xp::whereIn('user_id', $facultyIds)->get()->keyBy('user_id');

        // Pre-fetch all enrollments in one query
        $enrollments = Enrollment::whereIn('user_id', $facultyIds)
            ->with('course.contents')
            ->get()
            ->groupBy('user_id');

        // Pre-fetch all progress in one query
        $allContentIds = $enrollments->flatMap(fn ($group) => $group->flatMap(fn ($e) => $e->course?->contents ?? collect()))->pluck('id')->unique()->toArray();

        $allProgress = [];
        if (! empty($allContentIds)) {
            $allProgress = Progress::whereIn('user_id', $facultyIds)
                ->whereIn('content_id', $allContentIds)
                ->whereNotNull('completed_at')
                ->get()
                ->groupBy('user_id')
                ->map(fn ($group) => $group->count())
                ->toArray();
        }

        // Pre-fetch all quiz attempts in one query
        $allQuizAttempts = QuizAttempt::whereIn('user_id', $facultyIds)->get()->groupBy('user_id');

        $leaderboardData = $faculties->map(function ($faculty) use ($xpRecords, $enrollments, $allProgress, $allQuizAttempts) {
            $xp = $xpRecords[$faculty->id]?->xp ?? 0;

            $userEnrollments = $enrollments[$faculty->id] ?? collect();

            $completedCourses = $userEnrollments->filter(function ($e) use ($allProgress) {
                $contents = $e->course?->contents ?? collect();
                $totalContents = $contents->count();
                $completedContents = $allProgress[$e->user_id] ?? 0;

                return $totalContents > 0 && $completedContents === $totalContents;
            })->count();

            $attempts = $allQuizAttempts[$faculty->id] ?? collect();
            $avgScore = $attempts->avg('score') ?? 0;

            return [
                'id' => $faculty->id,
                'name' => $faculty->name,
                'xp' => $xp,
                'avgScore' => (int) $avgScore,
                'completedCourses' => $completedCourses,
                'isCurrentUser' => $faculty->id === auth()->id(),
            ];
        })->sortByDesc('xp')->values();

        $this->leaderboard = $leaderboardData->toArray();

        foreach ($this->leaderboard as $index => $entry) {
            if ($entry['isCurrentUser']) {
                $this->userRank = $index + 1;
                break;
            }
        }
    }

    public function render()
    {
        return view('livewire.faculty.dashboard')->layout('layouts.app');
    }
}
