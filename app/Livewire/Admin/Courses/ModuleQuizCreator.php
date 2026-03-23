<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use App\Models\Module;
use App\Models\ModuleQuiz;
use App\Models\Question;
use App\Models\Quiz;
use Livewire\Component;

class ModuleQuizCreator extends Component
{
    public ?Course $course = null;

    public ?ModuleQuiz $moduleQuiz = null;

    public ?int $moduleId = null;

    public array $questions = [];

    public array $availableModules = [];

    public function mount(?int $courseId = null, ?int $moduleQuizId = null): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($courseId) {
            $this->course = Course::findOrFail($courseId);
            $this->loadAvailableModules();
        }

        if ($moduleQuizId) {
            $this->moduleQuiz = ModuleQuiz::with('quiz.questions')->findOrFail($moduleQuizId);
            $this->loadModuleQuizData();
        }

        $this->addQuestion();
    }

    protected function loadAvailableModules(): void
    {
        $topicIds = $this->course->topics->pluck('id');

        $this->availableModules = Module::whereIn('topic_id', $topicIds)
            ->get()
            ->toArray();
    }

    protected function loadModuleQuizData(): void
    {
        if ($this->moduleQuiz) {
            $this->course = $this->moduleQuiz->module->topic->course;
            $this->loadAvailableModules();
            $this->moduleId = $this->moduleQuiz->module_id;

            if ($this->moduleQuiz->quiz) {
                foreach ($this->moduleQuiz->quiz->questions as $question) {
                    $this->questions[] = [
                        'id' => $question->id,
                        'question_text' => $question->question_text,
                        'type' => $question->type,
                        'options_text' => implode("\n", $question->options ?? []),
                        'correct_answer' => is_array($question->correct_answer)
                            ? implode(',', array_map(function ($i) {
                                return chr(65 + $i);
                            }, $question->correct_answer))
                            : ($question->correct_answer ?? ''),
                    ];
                }
            }
        }
    }

    public function addQuestion(): void
    {
        $this->questions[] = [
            'id' => null,
            'question_text' => '',
            'type' => 'multiple_choice',
            'options_text' => '',
            'correct_answer' => '',
        ];
    }

    public function removeQuestion(int $index): void
    {
        if (isset($this->questions[$index]['id'])) {
            Question::destroy($this->questions[$index]['id']);
        }
        unset($this->questions[$index]);
        $this->questions = array_values($this->questions);
    }

    public function save(): void
    {
        $this->validate([
            'moduleId' => 'required|exists:modules,id',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,true_false',
        ]);

        if (! $this->moduleQuiz) {
            $quiz = Quiz::create([
                'content_id' => Module::findOrFail($this->moduleId)->contents()->first()?->id,
            ]);

            $moduleQuiz = ModuleQuiz::create([
                'module_id' => $this->moduleId,
                'quiz_id' => $quiz->id,
            ]);

            $this->moduleQuiz = ModuleQuiz::with('quiz')->find($moduleQuiz->id);
        }

        $existingIds = [];

        foreach ($this->questions as $questionData) {
            $options = [];
            if ($questionData['type'] === 'multiple_choice' && ! empty($questionData['options_text'])) {
                $options = array_filter(array_map('trim', explode("\n", $questionData['options_text'])));
            }

            $correctAnswer = [];
            if ($questionData['type'] === 'multiple_choice' && ! empty($questionData['correct_answer'])) {
                $answers = array_filter(array_map('trim', explode(',', $questionData['correct_answer'])));
                foreach ($answers as $answer) {
                    $correctAnswer[] = ord(strtoupper($answer)) - 65;
                }
            } elseif ($questionData['type'] === 'true_false') {
                $correctAnswer = [$questionData['correct_answer'] === 'true' ? 0 : 1];
            }

            if (! empty($questionData['id'])) {
                $question = Question::where('id', $questionData['id'])->first();

                if ($question) {
                    $question->update([
                        'quiz_id' => $this->moduleQuiz->quiz->id,
                        'type' => $questionData['type'],
                        'question_text' => $questionData['question_text'],
                        'options' => $options,
                        'correct_answer' => $correctAnswer,
                    ]);
                    $existingIds[] = $question->id;
                }
            } else {
                $question = Question::create([
                    'quiz_id' => $this->moduleQuiz->quiz->id,
                    'type' => $questionData['type'],
                    'question_text' => $questionData['question_text'],
                    'options' => $options,
                    'correct_answer' => $correctAnswer,
                ]);
                $existingIds[] = $question->id;
            }
        }

        Question::where('quiz_id', $this->moduleQuiz->quiz->id)
            ->whereNotIn('id', $existingIds)
            ->delete();

        session()->flash('success', 'Module quiz saved successfully.');
        $this->redirectRoute('admin.courses.show', $this->course->id);
    }

    public function render()
    {
        return view('livewire.admin.courses.module-quiz-creator');
    }
}
