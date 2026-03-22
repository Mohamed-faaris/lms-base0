<?php

namespace App\Livewire\Admin\Courses;

use App\Models\ModuleQuiz;
use Livewire\Component;

class ModuleQuizViewer extends Component
{
    public ?ModuleQuiz $moduleQuiz = null;

    public int $moduleQuizId;

    public function mount(int $moduleQuizId): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $this->moduleQuizId = $moduleQuizId;
        $this->moduleQuiz = ModuleQuiz::with('quiz.questions', 'module.topic.course')->findOrFail($moduleQuizId);
    }

    public function render()
    {
        return view('livewire.admin.courses.module-quiz-viewer');
    }
}
