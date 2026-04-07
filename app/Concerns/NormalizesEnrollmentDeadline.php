<?php

namespace App\Concerns;

trait NormalizesEnrollmentDeadline
{
    protected function normalizeEnrollmentDeadline(?int $deadline): array
    {
        if ($deadline === null || $deadline === 0) {
            return [
                'daysLeft' => null,
                'isUrgent' => false,
                'isOverdue' => false,
            ];
        }

        $daysLeft = $deadline < 1_000_000_000
            ? $deadline
            : now()->diffInDays(now()->setTimestamp($deadline), false);

        return [
            'daysLeft' => $daysLeft,
            'isUrgent' => $daysLeft <= 3 && $daysLeft > 0,
            'isOverdue' => $daysLeft < 0,
        ];
    }
}
