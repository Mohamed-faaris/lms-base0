<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Content;
use App\Models\Course;
use App\Models\Module;
use App\Models\Progress;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\TimestampedQuiz;
use App\Models\Topic;
use Livewire\Component;

class Show extends Component
{
    public Course $course;

    public int $totalEnrollments = 0;

    public int $completedEnrollments = 0;

    public int $avgProgress = 0;

    public int $totalContent = 0;

    public array $expandedTopics = [];

    public array $expandedModules = [];

    // Modals
    public bool $showTopicModal = false;

    public bool $showModuleModal = false;

    public bool $showContentModal = false;

    public bool $showViewContentModal = false;

    // Editing
    public ?Topic $editingTopic = null;

    public ?Module $editingModule = null;

    public ?Content $editingContent = null;

    public ?Content $viewingContent = null;

    // Form fields
    public string $topicName = '';

    public string $topicDescription = '';

    public string $topicOrder = '';

    public string $moduleTitle = '';

    public string $moduleDescription = '';

    public string $moduleOrder = '';

    public ?int $selectedTopicId = null;

    public string $contentTitle = '';

    public string $contentBody = '';

    public string $contentType = 'video';

    public string $contentUrl = '';

    public ?int $selectedModuleId = null;

    // Quiz Modal
    public bool $showQuizModal = false;

    public bool $showViewQuizModal = false;

    public ?Quiz $editingQuiz = null;

    public ?Quiz $viewingQuiz = null;

    public string $quizQuestionText = '';

    public string $quizType = 'multiple_choice';

    public array $quizOptions = [];

    public string $quizCorrectAnswer = '';

    public string $quizOptionsText = '';

    public ?int $quizContentId = null;

    // Timestamped Quiz
    public bool $showTimestampedQuizModal = false;

    public ?TimestampedQuiz $editingTimestampedQuiz = null;

    public string $timestampedQuizTimestamp = '';

    public bool $showViewTimestampedQuizModal = false;

    public ?TimestampedQuiz $viewingTimestampedQuiz = null;

    public function mount(Course $course): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $this->course = Course::with(
            'topics.modules.contents.timestampedQuizzes.quiz.question',
            'topics.modules.contents.endQuiz.quiz.question',
            'topics.modules.moduleQuizzes.quiz.question',
            'enrollments'
        )->findOrFail($course->id);
        $this->calculateStats();

        foreach ($this->course->topics as $topic) {
            $this->expandedTopics[$topic->id] = true;
            foreach ($topic->modules as $module) {
                $this->expandedModules[$module->id] = true;
            }
        }
    }

    protected function calculateStats(): void
    {
        $this->totalEnrollments = $this->course->enrollments()->count();
        $contents = $this->getContentsThroughTopics();
        $this->totalContent = $contents->count();

        if ($this->totalEnrollments > 0 && $this->totalContent > 0) {
            $totalProgress = 0;
            $completed = 0;

            foreach ($this->course->enrollments as $enrollment) {
                $completedContent = Progress::where('user_id', $enrollment->user_id)
                    ->whereIn('content_id', $contents->pluck('id'))
                    ->whereNotNull('completed_at')
                    ->count();

                $progress = (int) round(($completedContent / $this->totalContent) * 100);
                $totalProgress += $progress;

                if ($progress === 100) {
                    $completed++;
                }
            }

            $this->avgProgress = (int) round($totalProgress / $this->totalEnrollments);
            $this->completedEnrollments = $completed;
        }
    }

    protected function getContentsThroughTopics()
    {
        $topicIds = $this->course->topics->pluck('id');

        return Content::whereIn('module_id', function ($query) use ($topicIds) {
            $query->select('id')
                ->from('modules')
                ->whereIn('topic_id', $topicIds);
        })->get();
    }

    public function toggleTopic(int $topicId): void
    {
        $this->expandedTopics[$topicId] = ! ($this->expandedTopics[$topicId] ?? false);
    }

    public function toggleModule(int $moduleId): void
    {
        $this->expandedModules[$moduleId] = ! ($this->expandedModules[$moduleId] ?? false);
    }

    public function toggleModuleQuizzes(int $moduleId): void
    {
        $key = "quiz_{$moduleId}";
        $this->expandedModules[$key] = ! ($this->expandedModules[$key] ?? false);
    }

    // Topic Modal
    public function openTopicModal($topic = null): void
    {
        if ($topic instanceof Topic) {
            $this->editingTopic = $topic;
            $this->topicName = $topic->name;
            $this->topicDescription = $topic->description ?? '';
            $this->topicOrder = (string) $topic->order;
        } else {
            $this->editingTopic = null;
            $this->topicName = '';
            $this->topicDescription = '';
            $this->topicOrder = (string) ($this->course->topics->max('order') + 1);
        }
        $this->showTopicModal = true;
    }

    public function saveTopic(): void
    {
        $this->validate([
            'topicName' => 'required|string|max:255',
            'topicOrder' => 'required|integer|min:1',
        ]);

        if ($this->editingTopic) {
            $this->editingTopic->update([
                'name' => $this->topicName,
                'description' => $this->topicDescription,
                'order' => (int) $this->topicOrder,
            ]);
        } else {
            Topic::create([
                'course_id' => $this->course->id,
                'name' => $this->topicName,
                'description' => $this->topicDescription,
                'order' => (int) $this->topicOrder,
            ]);
        }

        $this->refreshCourse();
        $this->closeTopicModal();
    }

    public function closeTopicModal(): void
    {
        $this->showTopicModal = false;
        $this->editingTopic = null;
    }

    // Module Modal
    public function openModuleModal($module = null): void
    {
        if ($module instanceof Module) {
            $this->editingModule = $module;
            $this->moduleTitle = $module->title;
            $this->moduleDescription = $module->description ?? '';
            $this->moduleOrder = (string) $module->order;
            $this->selectedTopicId = $module->topic_id;
        } else {
            $this->editingModule = null;
            $this->moduleTitle = '';
            $this->moduleDescription = '';
            $this->moduleOrder = '';
            // selectedTopicId is set from blade when clicking "+" button
        }
        $this->showModuleModal = true;
    }

    public function saveModule(): void
    {
        $this->validate([
            'moduleTitle' => 'required|string|max:255',
            'selectedTopicId' => 'required|exists:topics,id',
        ]);

        // Auto-generate order if not provided
        if (empty($this->moduleOrder)) {
            $topic = Topic::find($this->selectedTopicId);
            $this->moduleOrder = (string) ($topic->modules->max('order') ?? 0) + 1;
        }

        if ($this->editingModule) {
            $this->editingModule->update([
                'title' => $this->moduleTitle,
                'description' => $this->moduleDescription,
                'order' => (int) $this->moduleOrder,
                'topic_id' => $this->selectedTopicId,
            ]);
        } else {
            Module::create([
                'topic_id' => $this->selectedTopicId,
                'title' => $this->moduleTitle,
                'description' => $this->moduleDescription,
                'order' => (int) $this->moduleOrder,
            ]);
        }

        $this->refreshCourse();
        $this->closeModuleModal();
    }

    public function closeModuleModal(): void
    {
        $this->showModuleModal = false;
        $this->editingModule = null;
    }

    // Content Modal
    public function openContentModal($content = null): void
    {
        // Handle both Content object and ID
        if ($content instanceof Content) {
            $this->editingContent = $content;
        } elseif (is_numeric($content)) {
            $this->editingContent = Content::find($content);
        } else {
            $this->editingContent = null;
        }

        if ($this->editingContent) {
            $this->contentTitle = $this->editingContent->title;
            $this->contentBody = $this->editingContent->body ?? '';
            $this->contentType = $this->editingContent->type->value;
            $this->contentUrl = $this->editingContent->content_url ?? '';
            $this->selectedModuleId = $this->editingContent->module_id;
        } else {
            $this->contentTitle = '';
            $this->contentBody = '';
            $this->contentType = 'video';
            $this->contentUrl = '';
            // Don't reset selectedModuleId - it's set from blade for new content
        }
        $this->showContentModal = true;
    }

    public function saveContent(): void
    {
        $this->validate([
            'contentTitle' => 'required|string|max:255',
            'contentType' => 'required|in:video,article,quiz',
            'selectedModuleId' => 'required|exists:modules,id',
        ]);

        if ($this->editingContent) {
            $this->editingContent->update([
                'title' => $this->contentTitle,
                'body' => $this->contentBody,
                'type' => $this->contentType,
                'content_url' => $this->contentUrl,
            ]);
        } else {
            Content::create([
                'module_id' => $this->selectedModuleId,
                'title' => $this->contentTitle,
                'body' => $this->contentBody,
                'type' => $this->contentType,
                'content_url' => $this->contentUrl,
                'order' => $this->getNextContentOrder($this->selectedModuleId),
            ]);
        }

        $this->refreshCourse();
        $this->closeContentModal();
    }

    protected function getNextContentOrder(int $moduleId): int
    {
        return Content::where('module_id', $moduleId)->max('order') + 1;
    }

    public function closeContentModal(): void
    {
        $this->showContentModal = false;
        $this->editingContent = null;
    }

    // View Content Modal
    public function openViewContentModal($content): void
    {
        if (is_numeric($content)) {
            $this->viewingContent = Content::find($content);
        } else {
            $this->viewingContent = $content;
        }
        $this->showViewContentModal = true;
    }

    public function closeViewContentModal(): void
    {
        $this->showViewContentModal = false;
        $this->viewingContent = null;
    }

    // Quiz Modal
    public function openQuizModal($quiz = null, ?int $contentId = null): void
    {
        $this->quizContentId = $contentId;

        if ($quiz instanceof Quiz) {
            $this->editingQuiz = $quiz;
            $this->quizContentId = $quiz->content_id;
            if ($quiz->question) {
                $this->quizQuestionText = $quiz->question->question_text;
                $this->quizType = $quiz->question->type;
                $this->quizOptionsText = implode("\n", $quiz->question->options ?? []);
                $this->quizCorrectAnswer = is_array($quiz->question->correct_answer)
                    ? implode(',', array_map(function ($i) {
                        return chr(65 + $i);
                    }, $quiz->question->correct_answer))
                    : ($quiz->question->correct_answer ?? '');
            }
        } else {
            $this->editingQuiz = null;
            $this->quizQuestionText = '';
            $this->quizType = 'multiple_choice';
            $this->quizOptionsText = '';
            $this->quizCorrectAnswer = '';
        }
        $this->showQuizModal = true;
    }

    public function saveQuiz(): void
    {
        $this->validate([
            'quizQuestionText' => 'required|string',
            'quizType' => 'required|in:multiple_choice,true_false',
            'quizContentId' => 'required|exists:contents,id',
        ]);

        $options = [];
        if ($this->quizType === 'multiple_choice' && $this->quizOptionsText) {
            $options = array_filter(array_map('trim', explode("\n", $this->quizOptionsText)));
        }

        $correctAnswer = [];
        if ($this->quizType === 'multiple_choice') {
            $answers = array_filter(array_map('trim', explode(',', $this->quizCorrectAnswer)));
            foreach ($answers as $answer) {
                $correctAnswer[] = ord(strtoupper($answer)) - 65;
            }
        } else {
            $correctAnswer = [$this->quizCorrectAnswer === 'true' ? 0 : 1];
        }

        $question = Question::create([
            'type' => $this->quizType,
            'question_text' => $this->quizQuestionText,
            'options' => $options,
            'correct_answer' => $correctAnswer,
        ]);

        if ($this->editingQuiz) {
            $this->editingQuiz->update(['question_id' => $question->id]);
        } else {
            Quiz::create([
                'content_id' => $this->quizContentId,
                'question_id' => $question->id,
            ]);
        }

        $this->refreshCourse();
        $this->closeQuizModal();
    }

    public function closeQuizModal(): void
    {
        $this->showQuizModal = false;
        $this->editingQuiz = null;
        $this->quizContentId = null;
    }

    public function openViewQuizModal(Quiz $quiz): void
    {
        $this->viewingQuiz = $quiz;
        $this->showViewQuizModal = true;
    }

    public function closeViewQuizModal(): void
    {
        $this->showViewQuizModal = false;
        $this->viewingQuiz = null;
    }

    // Timestamped Quiz Modal
    public function openTimestampedQuizModal(?TimestampedQuiz $timestampedQuiz = null, ?int $contentId = null): void
    {
        $this->editingTimestampedQuiz = $timestampedQuiz;
        if ($timestampedQuiz) {
            $this->timestampedQuizTimestamp = $this->formatTimestamp($timestampedQuiz->timestamp);
        } else {
            $this->timestampedQuizTimestamp = '';
        }
        $this->showTimestampedQuizModal = true;
    }

    public function openViewTimestampedQuizModal(int $timestampedQuizId): void
    {
        $this->viewingTimestampedQuiz = TimestampedQuiz::with('quiz.question')->findOrFail($timestampedQuizId);
        $this->showViewTimestampedQuizModal = true;
    }

    public function closeViewTimestampedQuizModal(): void
    {
        $this->showViewTimestampedQuizModal = false;
        $this->viewingTimestampedQuiz = null;
    }

    public function saveTimestampedQuiz(): void
    {
        $this->validate([
            'timestampedQuizTimestamp' => 'required|string',
            'quizContentId' => 'required|exists:contents,id',
        ]);

        $seconds = $this->parseTimestamp($this->timestampedQuizTimestamp);

        if ($this->editingTimestampedQuiz) {
            $this->editingTimestampedQuiz->update(['timestamp' => $seconds]);
        } else {
            $quiz = Quiz::firstOrCreate(['content_id' => $this->quizContentId]);
            TimestampedQuiz::create([
                'content_id' => $this->quizContentId,
                'quiz_id' => $quiz->id,
                'timestamp' => $seconds,
            ]);
        }

        $this->refreshCourse();
        $this->closeTimestampedQuizModal();
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
            $hours = isset($matches[3]) ? (int) $matches[1] : 0;
            $minutes = isset($matches[3]) ? (int) $matches[2] : (int) $matches[1];
            $seconds = isset($matches[3]) ? (int) $matches[3] : 0;

            if (isset($matches[3])) {
                $hours = (int) $matches[1];
                $minutes = (int) $matches[2];
                $seconds = (int) $matches[3];
            } else {
                $hours = 0;
                $minutes = (int) $matches[1];
                $seconds = isset($matches[2]) ? (int) $matches[2] : 0;
            }

            return $hours * 3600 + $minutes * 60 + $seconds;
        }

        return (int) $timestamp;
    }

    public function closeTimestampedQuizModal(): void
    {
        $this->showTimestampedQuizModal = false;
        $this->editingTimestampedQuiz = null;
    }

    public function deleteContent(int $contentId): void
    {
        $content = Content::findOrFail($contentId);
        $content->delete();
        $this->closeViewContentModal();
        $this->refreshCourse();
    }

    protected function refreshCourse(): void
    {
        $this->course = Course::with(
            'topics.modules.contents.timestampedQuizzes.quiz.question',
            'topics.modules.contents.endQuiz.quiz.question',
            'topics.modules.moduleQuizzes.quiz.question',
            'enrollments'
        )->findOrFail($this->course->id);
    }

    public function render()
    {
        return view('livewire.admin.courses.show');
    }
}
