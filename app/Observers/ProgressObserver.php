<?php

namespace App\Observers;

use App\Models\Certificate;
use App\Models\Content;
use App\Models\Progress;

class ProgressObserver
{
    public function created(Progress $progress): void
    {
        $this->checkAndGenerateCertificate($progress);
    }

    public function updated(Progress $progress): void
    {
        $this->checkAndGenerateCertificate($progress);
    }

    protected function checkAndGenerateCertificate(Progress $progress): void
    {
        $userId = $progress->user_id;
        $content = Content::find($progress->content_id);
        
        if (!$content) {
            return;
        }

        $courseId = $content->course_id;

        $alreadyHasCertificate = Certificate::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();

        if ($alreadyHasCertificate) {
            return;
        }

        $courseContents = Content::where('course_id', $courseId)->get();
        $totalContents = $courseContents->count();

        $completedContents = Progress::where('user_id', $userId)
            ->whereIn('content_id', $courseContents->pluck('id'))
            ->whereNotNull('completed_at')
            ->count();

        if ($completedContents >= $totalContents && $totalContents > 0) {
            $lastProgress = Progress::where('user_id', $userId)
                ->whereIn('content_id', $courseContents->pluck('id'))
                ->whereNotNull('completed_at')
                ->orderBy('completed_at', 'desc')
                ->first();

            $completedAt = $lastProgress?->completed_at ?? now();

            Certificate::create([
                'user_id' => $userId,
                'course_id' => $courseId,
                'certificate_id' => Certificate::generateCertificateId($userId, $courseId, $completedAt),
                'completed_at' => $completedAt,
                'issued_at' => now(),
            ]);
        }
    }
}
