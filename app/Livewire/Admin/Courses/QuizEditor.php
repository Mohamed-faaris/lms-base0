<?php

namespace App\Livewire\Admin\Courses;

use App\Enums\ContentType;
use App\Enums\QuizKind;
use App\Models\Content;
use App\Models\Course;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class QuizEditor extends Component
{
    public Course $course;

    public Content $content;

    public string $placement = 'content';

    public ?Quiz $quiz = null;

    public array $questions = [];

    public string $timestamp = '';

    public function mount(Course $course, Content $content, string $placement, ?Quiz $quiz = null): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $this->course = $course;
        $this->content = Content::with(['module.topic.course', 'quiz.questions', 'endQuiz.questions', 'timestampedQuizzes.questions'])
            ->findOrFail($content->id);
        $this->placement = $placement;

        abort_unless($this->content->module?->topic?->course?->is($this->course), 404);

        if ($this->placementKind() === QuizKind::Timestamped) {
            if (! $this->content->type instanceof ContentType || $this->content->type !== ContentType::Video) {
                abort(404);
            }

            $this->quiz = $quiz?->kind === QuizKind::Timestamped ? $quiz->load('questions') : null;
        } elseif ($this->placementKind() === QuizKind::Content) {
            $this->quiz = $this->content->quiz?->load('questions');
        } else {
            $this->quiz = $this->content->endQuiz?->load('questions');
        }

        if ($this->quiz) {
            $this->questions = $this->quiz->questions
                ->map(fn (Question $question): array => [
                    'id' => $question->id,
                    'question_text' => $question->question_text,
                    'type' => $question->type,
                    'options' => $question->type === 'multiple_choice'
                        ? array_values($question->options ?? [])
                        : [],
                    'correct_answer' => $this->formatCorrectAnswer($question),
                ])
                ->values()
                ->all();

            if ($this->placementKind() === QuizKind::Timestamped) {
                $this->timestamp = $this->formatTimestamp($this->quiz->timestamp_seconds ?? 0);
            }
        }

        if ($this->questions === []) {
            $this->addQuestion();
        }
    }

    public function addQuestion(): void
    {
        $this->questions[] = [
            'id' => null,
            'question_text' => '',
            'type' => 'multiple_choice',
            'options' => ['', ''],
            'correct_answer' => '',
        ];
    }

    public function addOption(int $questionIndex): void
    {
        $this->questions[$questionIndex]['options'][] = '';
    }

    public function removeOption(int $questionIndex, int $optionIndex): void
    {
        unset($this->questions[$questionIndex]['options'][$optionIndex]);
        $this->questions[$questionIndex]['options'] = array_values($this->questions[$questionIndex]['options']);

        if (($this->questions[$questionIndex]['correct_answer'] ?? '') !== '' && (int) $this->questions[$questionIndex]['correct_answer'] === $optionIndex) {
            $this->questions[$questionIndex]['correct_answer'] = '';
        } elseif (($this->questions[$questionIndex]['correct_answer'] ?? '') !== '' && (int) $this->questions[$questionIndex]['correct_answer'] > $optionIndex) {
            $this->questions[$questionIndex]['correct_answer'] = (string) ((int) $this->questions[$questionIndex]['correct_answer'] - 1);
        }

        if (count($this->questions[$questionIndex]['options']) < 2) {
            $this->questions[$questionIndex]['options'][] = '';
        }
    }

    public function removeQuestion(int $index): void
    {
        if (isset($this->questions[$index]['id'])) {
            Question::destroy($this->questions[$index]['id']);
        }

        unset($this->questions[$index]);
        $this->questions = array_values($this->questions);

        if ($this->questions === []) {
            $this->addQuestion();
        }
    }

    public function save(): void
    {
        $this->validate($this->rules());
        $this->guardPlacement();

        $quiz = $this->resolveQuiz();
        $existingIds = [];

        foreach ($this->questions as $questionData) {
            $payload = [
                'quiz_id' => $quiz->id,
                'type' => $questionData['type'],
                'question_text' => $questionData['question_text'],
                'options' => $this->normalizeOptions($questionData),
                'correct_answer' => $this->normalizeCorrectAnswer($questionData),
            ];

            if (! empty($questionData['id'])) {
                $question = Question::query()->findOrFail($questionData['id']);
                $question->update($payload);
            } else {
                $question = Question::create($payload);
            }

            $existingIds[] = $question->id;
        }

        Question::query()
            ->where('quiz_id', $quiz->id)
            ->whereNotIn('id', $existingIds)
            ->delete();

        session()->flash('success', $this->placementKind()->label().' saved successfully.');

        $this->redirectRoute('admin.courses.content.show', $this->content->id);
    }

    protected function rules(): array
    {
        $rules = [
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,true_false',
            'questions.*.correct_answer' => 'required|string',
        ];

        foreach ($this->questions as $index => $question) {
            if (($question['type'] ?? 'multiple_choice') === 'multiple_choice') {
                $rules["questions.{$index}.options"] = 'required|array|min:2';
                $rules["questions.{$index}.options.*"] = 'required|string|max:255';
                $rules["questions.{$index}.correct_answer"] = 'required|integer|min:0';
            }
        }

        if ($this->placementKind() === QuizKind::Timestamped) {
            $rules['timestamp'] = 'required|string';
        }

        return $rules;
    }

    protected function guardPlacement(): void
    {
        if ($this->placementKind() === QuizKind::Content && $this->content->type !== ContentType::Quiz) {
            $this->addError('questions', 'Only quiz content items can use the primary quiz editor.');
            abort(422);
        }

        if ($this->placementKind() === QuizKind::Timestamped && $this->content->type !== ContentType::Video) {
            $this->addError('timestamp', 'Timestamped quizzes are only available for video content.');
            abort(422);
        }
    }

    protected function resolveQuiz(): Quiz
    {
        if ($this->placementKind() === QuizKind::Timestamped) {
            if ($this->quiz) {
                $this->quiz->update([
                    'timestamp_seconds' => $this->parseTimestamp($this->timestamp),
                ]);

                return $this->quiz->refresh();
            }

            $this->quiz = Quiz::create([
                'content_id' => $this->content->id,
                'kind' => $this->placementKind(),
                'timestamp_seconds' => $this->parseTimestamp($this->timestamp),
            ]);

            return $this->quiz;
        }

        $relation = $this->placementKind() === QuizKind::Content ? 'quiz' : 'endQuiz';
        $existingQuiz = $this->content->{$relation};

        if ($existingQuiz instanceof Quiz) {
            $existingQuiz->update([
                'kind' => $this->placementKind(),
                'timestamp_seconds' => null,
            ]);

            $this->quiz = $existingQuiz->refresh();

            return $this->quiz;
        }

        $this->quiz = Quiz::create([
            'content_id' => $this->content->id,
            'kind' => $this->placementKind(),
            'timestamp_seconds' => null,
        ]);

        return $this->quiz;
    }

    protected function normalizeOptions(array $questionData): array
    {
        if ($questionData['type'] !== 'multiple_choice') {
            return [];
        }

        return array_values(array_map(static fn (string $option): string => trim($option), $questionData['options'] ?? []));
    }

    protected function normalizeCorrectAnswer(array $questionData): array
    {
        if ($questionData['type'] === 'true_false') {
            return [$questionData['correct_answer'] === 'true' ? 0 : 1];
        }

        return [(int) $questionData['correct_answer']];
    }

    protected function formatCorrectAnswer(Question $question): string
    {
        if ($question->type === 'true_false') {
            return (($question->correct_answer ?? [1])[0] ?? 1) === 0 ? 'true' : 'false';
        }

        return (string) (($question->correct_answer ?? [0])[0] ?? 0);
    }

    protected function formatTimestamp(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    protected function parseTimestamp(string $timestamp): int
    {
        if (preg_match('/^(\d+):(\d+)(?::(\d+))?$/', $timestamp, $matches)) {
            if (isset($matches[3])) {
                return ((int) $matches[1] * 3600) + ((int) $matches[2] * 60) + (int) $matches[3];
            }

            return ((int) $matches[1] * 60) + (int) $matches[2];
        }

        return (int) $timestamp;
    }

    public function placementKind(): QuizKind
    {
        return QuizKind::from($this->placement);
    }

    public function render(): View
    {
        return view('livewire.admin.courses.quiz-editor');
    }
}
