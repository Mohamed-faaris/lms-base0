<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Content;
use Livewire\Component;

class ContentViewer extends Component
{
    public ?Content $content = null;

    public int $contentId;

    public function mount(int $contentId): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $this->contentId = $contentId;
        $this->content = Content::with([
            'module.topic.course',
            'timestampedQuizzes.quiz.question',
            'endQuiz.quiz.question',
        ])->findOrFail($contentId);
    }

    protected function formatTimestamp(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    public function render()
    {
        return view('livewire.admin.courses.content-viewer');
    }
}
