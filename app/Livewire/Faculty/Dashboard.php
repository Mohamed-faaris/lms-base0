<?php

namespace App\Livewire\Faculty;

use App\Concerns\NormalizesEnrollmentDeadline;
use App\Models\Badge;
use App\Models\Enrollment;
use App\Models\Progress;
use App\Models\Streak;
use App\Models\Xp;
use Illuminate\Support\Collection;
use Livewire\Component;

class Dashboard extends Component
{
    use NormalizesEnrollmentDeadline;

    public Collection $enrolledCourses;

    public int $totalXp = 0;

    public int $level = 1;

    public int $streak = 0;

    public int $weeklyStreak = 0;

    public ?Badge $currentBadge = null;

    public ?Badge $nextBadge = null;

    public int $xpToNextLevel = 500;

    public array $weekDays = [];

    public function mount(): void
    {
        $this->loadUserData();
        $this->loadWeekDays();
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

        // Get current and next badge
        $this->currentBadge = Badge::where('conditions', '!=', '[]')
            ->get()
            ->sortBy(fn ($b) => $b->conditions['min_xp'] ?? 0)
            ->first(fn ($b) => ($b->conditions['min_xp'] ?? 0) <= $this->totalXp);

        $this->nextBadge = Badge::where('conditions', '!=', '[]')
            ->get()
            ->sortBy(fn ($b) => $b->conditions['min_xp'] ?? 0)
            ->first(fn ($b) => ($b->conditions['min_xp'] ?? 0) > $this->totalXp);

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
                    'thumbnailUrl' => $course ? ($course->getFirstMediaUrl('course-thumbnail') ?: $course->courseMeta?->thumbnail) : null,
                ];
            });
    }

    protected function loadWeekDays(): void
    {
        $weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $currentDay = now()->dayOfWeek; // 1 = Monday, 7 = Sunday

        $this->weekDays = array_map(function ($day, $index) use ($currentDay) {
            return [
                'name' => $day,
                'isToday' => ($index + 1) === $currentDay,
                'isCompleted' => false, // Will be updated based on streak
            ];
        }, $weekDays, array_keys($weekDays));
    }

    public function render()
    {
        return view('livewire.faculty.dashboard')->layout('layouts.app');
    }
}
