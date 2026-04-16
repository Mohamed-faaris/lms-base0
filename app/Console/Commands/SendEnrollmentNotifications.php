<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Notifications\DeadlineApproachingNotification;
use App\Notifications\DeadlineReachedNotification;
use App\Notifications\NotCompletedNotification;
use App\Notifications\NotStartedNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEnrollmentNotifications extends Command
{
    protected $signature = 'notifications:enrollment-check';

    protected $description = 'Send enrollment notifications based on deadline and progress';

    public function handle(): int
    {
        $enrollments = Enrollment::with(['user', 'course', 'batch'])
            ->whereNotNull('enrolled_at')
            ->get();

        $notStartedSent = 0;
        $deadlineApproachingSent = 0;
        $notCompletedSent = 0;
        $deadlineReachedSent = 0;

        foreach ($enrollments as $enrollment) {
            $progress = $this->getProgress($enrollment);
            $daysSinceEnrolled = $enrollment->enrolled_at
                ? Carbon::parse($enrollment->enrolled_at)->diffInDays(now())
                : 0;
            $daysUntilDeadline = $this->getDaysUntilDeadline($enrollment);
            $batchKey = $enrollment->batch?->key ?? $enrollment->course?->slug ?? 'unknown';
            $courseTitle = $enrollment->course?->title ?? 'Unknown Course';

            if ($progress === 0 && $daysSinceEnrolled >= 2) {
                $enrollment->user->notify(new NotStartedNotification(
                    $courseTitle,
                    $batchKey,
                    $daysSinceEnrolled
                ));
                $notStartedSent++;
            }

            if ($progress > 0 && $progress < 100 && $daysUntilDeadline <= 2 && $daysUntilDeadline > 0) {
                $enrollment->user->notify(new NotCompletedNotification(
                    $courseTitle,
                    $batchKey,
                    $daysUntilDeadline,
                    $progress
                ));
                $notCompletedSent++;
            }

            if ($daysUntilDeadline <= 2 && $daysUntilDeadline > 0 && $progress < 100) {
                $enrollment->user->notify(new DeadlineApproachingNotification(
                    $courseTitle,
                    $batchKey,
                    $daysUntilDeadline
                ));
                $deadlineApproachingSent++;
            }

            if ($daysUntilDeadline <= 0 && $progress < 100) {
                $enrollment->user->notify(new DeadlineReachedNotification(
                    $courseTitle,
                    $batchKey
                ));
                $deadlineReachedSent++;
            }
        }

        $this->info("Notifications sent: Not Started: {$notStartedSent}, Deadline Approaching: {$deadlineApproachingSent}, Not Completed: {$notCompletedSent}, Deadline Reached: {$deadlineReachedSent}");

        return Command::SUCCESS;
    }

    protected function getProgress(Enrollment $enrollment): int
    {
        $course = $enrollment->course;
        if (! $course) {
            return 0;
        }

        $totalContents = $course->contents()->count();
        if ($totalContents === 0) {
            return 0;
        }

        $completedContents = $enrollment->user()
            ->progress()
            ->whereIn('content_id', $course->contents()->pluck('id'))
            ->count();

        return (int) round(($completedContents / $totalContents) * 100);
    }

    protected function getDaysUntilDeadline(Enrollment $enrollment): int
    {
        if (! $enrollment->deadline) {
            return PHP_INT_MAX;
        }

        $deadlineTimestamp = is_numeric($enrollment->deadline)
            ? $enrollment->deadline
            : Carbon::parse($enrollment->deadline)->timestamp;

        return Carbon::now()->diffInDays(Carbon::createFromTimestamp($deadlineTimestamp), false);
    }
}
