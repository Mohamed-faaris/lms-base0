<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Content;
use App\Models\Course;
use App\Models\EndQuiz;
use App\Models\Question;
use App\Models\Quiz;
use Livewire\Component;

class EndQuizCreator extends Component
{
    public ?Course $course = null;

    public ?EndQuiz $endQuiz = null;

    public ?int $contentId = null;

    public array $questions = [];

    public array $availableContents = [];

    public function mount(?int $courseId = null, ?int $endQuizId = null): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($courseId) {
            $this->course = Course::findOrFail($courseId);
            $this->loadAvailableContents();
        }

        if ($endQuizId) {
            $this->endQuiz = EndQuiz::with('quiz.questions')->findOrFail($endQuizId);
            $this->loadEndQuizData();
        }

        $this->addQuestion();
    }

    protected function loadAvailableContents(): void
    {
        $topicIds = $this->course->topics->pluck('id');
        $moduleIds = \App\Models\Module::whereIn('topic_id', $topicIds)->pluck('id');

        $this->availableContents = Content::whereIn('module_id', $moduleIds)
            ->where('type', 'video')
            ->get()
            ->toArray();
    }

    protected function loadEndQuizData(): void
    {
        if ($this->endQuiz) {
            $this->course = $this->endQuiz->content->module->topic->course;
            $this->loadAvailableContents();
            $this->contentId = $this->endQuiz->content_id;

            if ($this->endQuiz->quiz) {
                foreach ($this->endQuiz->quiz->questions as $question) {
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
            'contentId' => 'required|exists:contents,id',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,true_false',
        ]);

        if (! $this->endQuiz) {
            $this->endQuiz = EndQuiz::create([
                'content_id' => $this->contentId,
                'quiz_id' => Quiz::create(['content_id' => $this->contentId])->id,
            ]);
        }

        $quiz = $this->endQuiz->quiz;
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
                        'quiz_id' => $quiz->id,
                        'type' => $questionData['type'],
                        'question_text' => $questionData['question_text'],
                        'options' => $options,
                        'correct_answer' => $correctAnswer,
                    ]);
                    $existingIds[] = $question->id;
                }
            } else {
                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'type' => $questionData['type'],
                    'question_text' => $questionData['question_text'],
                    'options' => $options,
                    'correct_answer' => $correctAnswer,
                ]);
                $existingIds[] = $question->id;
            }
        }

        $quiz->update(['question_id' => $existingIds[0] ?? null]);
        Question::where('quiz_id', $quiz->id)
            ->whereNotIn('id', $existingIds)
            ->delete();

        session()->flash('success', 'End quiz saved successfully.');
        $this->redirectRoute('admin.courses.show', $this->course->id);
    }

    public function render()
    {
        return view('livewire.admin.courses.end-quiz-creator');
    }
}
