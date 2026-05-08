<?php

namespace App\Services;

use App\Models\BehavioralEvent;
use App\Models\Content;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Collection;

class BehavioralAnalyticsService
{
    public function recordEvent(
        User $user,
        string $eventType,
        ?Content $content = null,
        ?Course $course = null,
        array $metadata = []
    ): BehavioralEvent {
        return BehavioralEvent::create([
            'user_id' => $user->id,
            'content_id' => $content?->id,
            'course_id' => $course?->id,
            'event_type' => $eventType,
            'duration_seconds' => $metadata['duration_seconds'] ?? null,
            'video_timestamp' => $metadata['video_timestamp'] ?? null,
            'pause_count' => $metadata['pause_count'] ?? null,
            'seek_position' => $metadata['seek_position'] ?? null,
            'metadata' => $metadata,
        ]);
    }

    public function getUserBehavioralScores(int $userId, ?int $courseId = null): array
    {
        $query = BehavioralEvent::where('user_id', $userId)
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId));

        $events = $query->orderBy('event_timestamp')->get();

        if ($events->isEmpty()) {
            return [
                'engagement_score' => 0,
                'consistency_score' => 0,
                'focus_score' => 0,
                'compliance_score' => 0,
                'total_events' => 0,
            ];
        }

        $engagementScore = $this->calculateEngagementScore($events);
        $consistencyScore = $this->calculateConsistencyScore($events);
        $focusScore = $this->calculateFocusScore($events);
        $complianceScore = $this->calculateComplianceScore($events);

        return [
            'engagement_score' => round($engagementScore, 1),
            'consistency_score' => round($consistencyScore, 1),
            'focus_score' => round($focusScore, 1),
            'compliance_score' => round($complianceScore, 1),
            'total_events' => $events->count(),
        ];
    }

    private function calculateEngagementScore(Collection $events): float
    {
        $videoPlayEvents = $events->where('event_type', 'video_play')->count();
        $videoCompleteEvents = $events->where('event_type', 'video_completed')->count();
        $quizStartedEvents = $events->where('event_type', 'quiz_started')->count();
        $quizCompletedEvents = $events->where('event_type', 'quiz_completed')->count();

        $watchDuration = $events->whereIn('event_type', ['video_play', 'video_pause'])
            ->sum('duration_seconds') ?? 0;

        $maxScore = 100;
        $videoPlayWeight = 15;
        $videoCompleteWeight = 25;
        $quizWeight = 20;
        $durationWeight = 40;

        $videoPlayScore = min($videoPlayWeight, $videoPlayEvents * 3);
        $videoCompleteScore = min($videoCompleteWeight, $videoCompleteEvents * 10);
        $quizScore = min($quizWeight, $quizStartedEvents * 5 + $quizCompletedEvents * 10);
        $durationScore = min($durationWeight, min($watchDuration / 60, $durationWeight));

        return $videoPlayScore + $videoCompleteScore + $quizScore + $durationScore;
    }

    private function calculateConsistencyScore(Collection $events): float
    {
        $sessionStartEvents = $events->where('event_type', 'session_start');
        $sessionEndEvents = $events->where('event_type', 'session_end');

        if ($sessionStartEvents->isEmpty()) {
            return 0;
        }

        $totalSessions = $sessionStartEvents->count();
        $completedSessions = $sessionEndEvents->count();

        $revisitEvents = $events->where('event_type', 'content_revisit')->count();

        $sessionCompletionScore = ($completedSessions / $totalSessions) * 50;
        $noRevisitScore = max(0, 50 - ($revisitEvents * 2));

        return $sessionCompletionScore + $noRevisitScore;
    }

    private function calculateFocusScore(Collection $events): float
    {
        $tabSwitchEvents = $events->where('event_type', 'tab_switch')->count();
        $windowBlurEvents = $events->where('event_type', 'window_blur')->count();
        $inactivityEvents = $events->where('event_type', 'inactivity_detected')->count();

        $totalFocusLoss = $tabSwitchEvents + $windowBlurEvents + $inactivityEvents;

        $maxScore = 100;
        $deduction = $totalFocusLoss * 10;

        return max(0, $maxScore - $deduction);
    }

    private function calculateComplianceScore(Collection $events): float
    {
        $requiredWatchPercent = 90;

        $completedVideos = $events->where('event_type', 'video_completed')->count();
        $startedQuizzes = $events->where('event_type', 'quiz_started')->count();

        $videoCompliance = min(50, $completedVideos * 10);
        $quizCompliance = min(50, $startedQuizzes * 25);

        return $videoCompliance + $quizCompliance;
    }

    public function getFacultyAnalyticsForAdmin(User $admin): Collection
    {
        $facultyQuery = User::query()->where('role', 'faculty');

        if ($admin->isManager()) {
            $facultyQuery = $admin->scopedFacultyQuery();
        }

        $facultyIds = $facultyQuery->pluck('id');

        return User::whereIn('id', $facultyIds)->get()->map(function ($faculty) {
            $scores = $this->getUserBehavioralScores($faculty->id);

            return [
                'id' => $faculty->id,
                'name' => $faculty->name,
                'email' => $faculty->email,
                'department' => $faculty->department?->value ?? $faculty->department,
                'engagement_score' => $scores['engagement_score'],
                'consistency_score' => $scores['consistency_score'],
                'focus_score' => $scores['focus_score'],
                'compliance_score' => $scores['compliance_score'],
                'total_events' => $scores['total_events'],
                'overall_score' => round(
                    ($scores['engagement_score'] + $scores['consistency_score'] +
                     $scores['focus_score'] + $scores['compliance_score']) / 4,
                    1
                ),
            ];
        })->sortByDesc('overall_score')->values();
    }

    public function getCourseAnalytics(int $courseId): array
    {
        $events = BehavioralEvent::where('course_id', $courseId)->get();

        $enrolledUsers = \App\Models\Enrollment::where('course_id', $courseId)
            ->distinct()
            ->count('user_id');

        $activeUsers = $events->distinct('user_id')->count('user_id');

        $avgEngagement = 0;
        $avgFocus = 0;

        if ($activeUsers > 0) {
            $userIds = $events->distinct('user_id')->pluck('user_id');
            $totalEngagement = 0;
            $totalFocus = 0;

            foreach ($userIds as $userId) {
                $scores = $this->getUserBehavioralScores($userId, $courseId);
                $totalEngagement += $scores['engagement_score'];
                $totalFocus += $scores['focus_score'];
            }

            $avgEngagement = $totalEngagement / $activeUsers;
            $avgFocus = $totalFocus / $activeUsers;
        }

        return [
            'course_id' => $courseId,
            'enrolled_count' => $enrolledUsers,
            'active_learners' => $activeUsers,
            'average_engagement_score' => round($avgEngagement, 1),
            'average_focus_score' => round($avgFocus, 1),
            'total_interactions' => $events->count(),
        ];
    }
}
