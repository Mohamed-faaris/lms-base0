<?php

namespace App\Livewire\Admin\Courses;

use App\Models\EndQuiz;
use Livewire\Component;

class EndQuizViewer extends Component
{
    public ?EndQuiz $endQuiz = null;

    public int $endQuizId;

    public function mount(int $endQuizId): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $this->endQuizId = $endQuizId;
        $this->endQuiz = EndQuiz::with('quiz.questions', 'content.module.topic.course')->findOrFail($endQuizId);
    }

    public function render()
    {
        return view('livewire.admin.courses.end-quiz-viewer');
    }
}
