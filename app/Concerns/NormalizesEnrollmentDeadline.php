<?php

namespace App\Concerns;

trait NormalizesEnrollmentDeadline
{
    protected function normalizeEnrollmentDeadline(?int $deadline): array
    {
        if ($deadline === null || $deadline === 0) {
            return [
                'daysLeft' => null,
                'hoursLeft' => null,
                'isUrgent' => false,
                'isOverdue' => false,
                'label' => 'No deadline',
                'compactLabel' => 'No deadline',
            ];
        }

        $now = now();
        $isLegacyDeadline = $deadline < 1_000_000_000;

        if ($isLegacyDeadline) {
            $daysLeft = $deadline;
            $hoursLeft = null;
        } else {
            $secondsUntilDeadline = $deadline - $now->timestamp;
            $daysLeft = $this->normalizeDeadlineDayDifference($secondsUntilDeadline);
            $hoursLeft = $daysLeft < 1 && $daysLeft >= 0
                ? max(1, (int) ceil($secondsUntilDeadline / 3600))
                : null;
        }

        $isOverdue = $daysLeft < 0;
        $isUrgent = $daysLeft <= 3 && $daysLeft > 0;

        return [
            'daysLeft' => $daysLeft,
            'hoursLeft' => $hoursLeft,
            'isUrgent' => $isUrgent,
            'isOverdue' => $isOverdue,
            'label' => $this->formatEnrollmentDeadlineLabel($daysLeft, $hoursLeft, $isOverdue),
            'compactLabel' => $this->formatEnrollmentDeadlineLabel($daysLeft, $hoursLeft, $isOverdue, compact: true),
        ];
    }

    protected function formatEnrollmentDeadlineLabel(?int $daysLeft, ?int $hoursLeft, bool $isOverdue, bool $compact = false): string
    {
        if ($isOverdue) {
            return 'Overdue';
        }

        if ($hoursLeft !== null) {
            $suffix = $compact ? 'h left' : ' hour'.($hoursLeft === 1 ? '' : 's').' left';

            return $hoursLeft.$suffix;
        }

        if ($daysLeft === null) {
            return 'No deadline';
        }

        $suffix = $compact ? 'd left' : ' day'.($daysLeft === 1 ? '' : 's').' left';

        return $daysLeft.$suffix;
    }

    protected function normalizeDeadlineDayDifference(int $secondsUntilDeadline): int
    {
        if ($secondsUntilDeadline >= 0) {
            return (int) ceil($secondsUntilDeadline / 86400);
        }

        return (int) floor($secondsUntilDeadline / 86400);
    }
}
