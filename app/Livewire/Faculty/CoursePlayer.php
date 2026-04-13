<?php

namespace App\Livewire\Faculty;

use App\Models\Comment;
use App\Models\Content;
use App\Models\Course;
use App\Models\Progress;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Support\Collection;
use Livewire\Component;

class CoursePlayer extends Component
{
    public Course $course;

    public Content $currentModule;

    public Collection $modules;

    public int $totalModules = 0;

    public int $completedModules = 0;

    public int $courseProgress = 0;

    public bool $sidebarOpen = true;

    public bool $mobileDrawerOpen = false;

    public bool $showQuiz = false;

    public bool $quizSubmitted = false;

    public ?int $quizScore = null;

    public array $quizAnswers = [];

    public bool $showFeedback = false;

    public bool $showPuzzle = false;

    public Collection $comments;

    public string $newComment = '';

    public array $replyDrafts = [];

    public ?int $activeReplyCommentId = null;

    public ?Quiz $activeQuiz = null;

    public string $activeQuizContext = 'main';

    public string $activeQuizTitle = 'Module Quiz';

    public int $activeQuizPassPercentage = 0;

    public array $quizQuestions = [];

    public array $completedTimestampedQuizIds = [];

    public ?int $activeTimestampedQuizId = null;

    public function mount(?Course $course = null): void
    {
        $user = auth()->user();

        if (! $course || ! $course->exists) {
            $this->course = Course::whereHas('enrollments', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->firstOrFail();
        } else {
            $this->course = $course;
        }

        $this->loadCourseData();
    }

    protected function loadCourseData(): void
    {
        $user = auth()->user();

        $this->course->loadMissing('topics.modules.contents');

        $contentIds = $this->course->topics
            ->sortBy('order')
            ->flatMap(fn ($topic) => $topic->modules->sortBy('order'))
            ->flatMap(fn ($module) => $module->contents->sortBy('order'))
            ->pluck('id');

        $this->modules = Content::query()
            ->with([
                'quiz.questions',
                'endQuiz.questions',
                'timestampedQuizzes.questions',
            ])
            ->whereIn('id', $contentIds)
            ->orderBy('id')
            ->get();

        $this->totalModules = $this->modules->count();

        if ($this->totalModules === 0) {
            $this->comments = collect();

            return;
        }

        $completedContentIds = Progress::query()
            ->where('user_id', $user->id)
            ->whereIn('content_id', $this->modules->pluck('id'))
            ->whereNotNull('completed_at')
            ->pluck('content_id')
            ->toArray();

        $this->completedModules = count($completedContentIds);
        $this->courseProgress = (int) round(($this->completedModules / $this->totalModules) * 100);

        $firstIncomplete = $this->modules->first(fn ($module) => ! in_array($module->id, $completedContentIds, true));
        $currentModuleId = $this->currentModule->id ?? ($firstIncomplete?->id ?? $this->modules->last()->id);

        $this->modules->transform(function (Content $module, int $key) use ($completedContentIds) {
            $status = in_array($module->id, $completedContentIds, true) ? 'completed' : 'locked';

            if ($module->id === ($this->currentModule->id ?? null)) {
                $status = 'in-progress';
            }

            $previousCompleted = $key === 0 || in_array($this->modules[$key - 1]->id, $completedContentIds, true);
            if ($status === 'locked' && $previousCompleted) {
                $status = 'unlocked';
            }

            $meta = is_array($module->content_meta) ? $module->content_meta : [];
            $mainQuiz = $module->type?->value === 'quiz' ? $module->quiz : $module->endQuiz;

            $module->status = $status;
            $module->duration = $meta['duration'] ?? '15:00';
            $module->videoId = $meta['youtube_id'] ?? $this->extractYoutubeId($module->content_url) ?? 'dQw4w9WgXcQ';
            $module->watchRequirementPercent = max(50, min(100, (int) ($meta['watch_requirement_percent'] ?? 90)));
            $module->startTimeSeconds = max(0, (int) ($meta['start_time_seconds'] ?? 0));
            $module->endTimeSeconds = max(0, (int) ($meta['end_time_seconds'] ?? 0));
            $module->seekForwardEnabled = (bool) ($meta['seek_forward_enabled'] ?? false);
            $module->allowSpeedChange = (bool) ($meta['allow_speed_change'] ?? true);
            $module->allowCaptions = (bool) ($meta['allow_captions'] ?? true);
            $module->rewindSeconds = max(5, (int) ($meta['rewind_seconds'] ?? 10));
            $module->forwardSeconds = max(5, (int) ($meta['forward_seconds'] ?? 10));
            $module->isVideoLesson = $module->type?->value === 'video' && filled($module->videoId);
            $module->mainQuiz = $mainQuiz;
            $module->hasMainQuiz = $mainQuiz instanceof Quiz;
            $module->mainQuizPassPercentage = $mainQuiz?->passingScore() ?? 0;
            $module->mainQuizButtonLabel = $mainQuiz instanceof Quiz ? 'Take Module Quiz' : 'Mark Lesson Complete';
            $module->timestampedQuizSummaries = $module->timestampedQuizzes
                ->sortBy('timestamp_seconds')
                ->map(fn (Quiz $quiz) => [
                    'id' => $quiz->id,
                    'timestamp_seconds' => $quiz->timestamp_seconds ?? 0,
                    'score_percentage' => $quiz->passingScore(),
                ])
                ->values()
                ->all();

            return $module;
        });

        $this->currentModule = $this->modules->firstWhere('id', $currentModuleId) ?? $this->modules->first();
        $this->syncCurrentModuleStatus($completedContentIds);
        $this->completedTimestampedQuizIds = $this->resolveCompletedTimestampedQuizIds();
        $this->loadComments();
    }

    protected function syncCurrentModuleStatus(array $completedContentIds): void
    {
        if (in_array($this->currentModule->id, $completedContentIds, true)) {
            $this->currentModule->status = 'completed';

            return;
        }

        $this->currentModule->status = 'in-progress';
    }

    protected function resolveCompletedTimestampedQuizIds(): array
    {
        $timestampedQuizzes = $this->currentModule->timestampedQuizzes ?? collect();

        if ($timestampedQuizzes->isEmpty()) {
            return [];
        }

        $attemptsByQuiz = QuizAttempt::query()
            ->where('user_id', auth()->id())
            ->whereIn('quiz_id', $timestampedQuizzes->pluck('id'))
            ->get()
            ->groupBy('quiz_id');

        return $timestampedQuizzes
            ->filter(function (Quiz $quiz) use ($attemptsByQuiz) {
                $attempts = $attemptsByQuiz->get($quiz->id, collect());

                if ($attempts->isEmpty()) {
                    return false;
                }

                return $attempts->max('score') >= $quiz->passingScore();
            })
            ->pluck('id')
            ->values()
            ->all();
    }

    protected function loadComments(): void
    {
        $comments = Comment::query()
            ->with('user')
            ->where('content_id', $this->currentModule->id)
            ->latest()
            ->get();

        $groupedComments = $comments->groupBy('parent_comment_id');

        $this->comments = $this->buildCommentThread($groupedComments, null);
    }

    protected function buildCommentThread(Collection $groupedComments, ?int $parentCommentId): Collection
    {
        return ($groupedComments->get($parentCommentId) ?? collect())
            ->map(function (Comment $comment) use ($groupedComments) {
                $comment->threadReplies = $this->buildCommentThread($groupedComments, $comment->id);

                return $comment;
            })
            ->values();
    }

    private function extractYoutubeId(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $parsedUrl = parse_url(trim($url));
        if (! $parsedUrl) {
            return null;
        }

        $host = strtolower($parsedUrl['host'] ?? '');
        $path = $parsedUrl['path'] ?? '';

        if (! empty($host) && str_contains($host, 'youtu.be')) {
            return ltrim($path, '/');
        }

        if (! empty($host) && str_contains($host, 'youtube.com')) {
            if (isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $queryParams);
                if (! empty($queryParams['v'])) {
                    return $queryParams['v'];
                }
            }

            $segments = array_values(array_filter(explode('/', trim($path, '/'))));
            if (! empty($segments)) {
                if (in_array($segments[0], ['embed', 'v'], true) && isset($segments[1])) {
                    return $segments[1];
                }

                return $segments[0];
            }
        }

        return null;
    }

    protected function transformQuestions(Quiz $quiz): array
    {
        return $quiz->questions
            ->map(function (Question $question): array {
                $options = $question->type === 'true_false'
                    ? [
                        ['id' => '0', 'text' => 'True'],
                        ['id' => '1', 'text' => 'False'],
                    ]
                    : collect($question->options ?? [])
                        ->values()
                        ->map(fn (string $option, int $index) => ['id' => (string) $index, 'text' => $option])
                        ->all();

                return [
                    'id' => $question->id,
                    'question' => $question->question_text,
                    'type' => $question->type,
                    'options' => $options,
                    'correctAnswer' => collect($question->correct_answer ?? [])
                        ->map(fn ($answer) => (string) $answer)
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    protected function loadQuiz(Quiz $quiz, string $context): void
    {
        $this->activeQuiz = $quiz->loadMissing('questions');
        $this->activeQuizContext = $context;
        $this->activeTimestampedQuizId = $context === 'timestamped' ? $quiz->id : null;
        $this->activeQuizPassPercentage = $quiz->passingScore();
        $this->activeQuizTitle = $context === 'timestamped' ? 'Checkpoint Quiz' : 'Module Quiz';
        $this->quizQuestions = $this->transformQuestions($this->activeQuiz);
        $this->quizAnswers = [];
        $this->quizScore = null;
        $this->quizSubmitted = false;
        $this->showQuiz = true;
    }

    protected function clearQuizState(): void
    {
        $this->showQuiz = false;
        $this->quizSubmitted = false;
        $this->quizScore = null;
        $this->quizAnswers = [];
        $this->quizQuestions = [];
        $this->activeQuiz = null;
        $this->activeQuizContext = 'main';
        $this->activeQuizTitle = 'Module Quiz';
        $this->activeQuizPassPercentage = 0;
        $this->activeTimestampedQuizId = null;
    }

    public function selectModule(int $moduleId): void
    {
        $module = $this->modules->firstWhere('id', $moduleId);
        if ($module && $module->status !== 'locked') {
            $this->currentModule = $module;
            $this->clearQuizState();
            $this->mobileDrawerOpen = false;
            $this->newComment = '';
            $this->replyDrafts = [];
            $this->activeReplyCommentId = null;
            $this->completedTimestampedQuizIds = $this->resolveCompletedTimestampedQuizIds();
            $this->loadComments();
        }
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    public function toggleMobileDrawer(): void
    {
        $this->mobileDrawerOpen = ! $this->mobileDrawerOpen;
    }

    public function startQuiz(bool $watchRequirementMet = false): void
    {
        $this->loadCourseData();

        if ($this->currentModule->isVideoLesson && $this->currentModule->status !== 'completed' && ! $watchRequirementMet) {
            return;
        }

        if (! $this->currentModule->hasMainQuiz) {
            $this->completeCurrentModule();
            $this->loadCourseData();

            return;
        }

        $this->loadQuiz($this->currentModule->mainQuiz, 'main');
    }

    public function openTimestampedQuiz(int $quizId): void
    {
        $this->loadCourseData();

        if (in_array($quizId, $this->completedTimestampedQuizIds, true)) {
            return;
        }

        $quiz = $this->currentModule->timestampedQuizzes
            ->firstWhere('id', $quizId);

        if (! $quiz instanceof Quiz) {
            return;
        }

        $this->loadQuiz($quiz, 'timestamped');
    }

    public function resetQuiz(): void
    {
        $this->clearQuizState();
    }

    public function retakeQuiz(): void
    {
        $this->quizSubmitted = false;
        $this->quizScore = null;
        $this->quizAnswers = [];
    }

    public function setAnswer(string $questionId, string $answerId): void
    {
        $this->quizAnswers[$questionId] = $answerId;
    }

    protected function allQuestionsAnswered(): bool
    {
        return count($this->quizAnswers) >= count($this->quizQuestions);
    }

    protected function calculateQuizScore(): int
    {
        $correctAnswers = collect($this->quizQuestions)
            ->filter(function (array $question) {
                $selectedAnswer = $this->quizAnswers[(string) $question['id']] ?? null;

                return $selectedAnswer !== null && in_array((string) $selectedAnswer, $question['correctAnswer'], true);
            })
            ->count();

        return (int) round(($correctAnswers / max(count($this->quizQuestions), 1)) * 100);
    }

    public function submitQuiz(): void
    {
        if (! $this->activeQuiz instanceof Quiz || ! $this->allQuestionsAnswered()) {
            return;
        }

        $this->quizScore = $this->calculateQuizScore();
        $this->quizSubmitted = true;

        QuizAttempt::query()->create([
            'user_id' => auth()->id(),
            'quiz_id' => $this->activeQuiz->id,
            'score' => $this->quizScore,
            'attempted_at' => now(),
        ]);

        if ($this->quizScore < $this->activeQuizPassPercentage) {
            return;
        }

        if ($this->activeQuizContext === 'timestamped') {
            $this->completedTimestampedQuizIds[] = $this->activeQuiz->id;
            $this->completedTimestampedQuizIds = array_values(array_unique($this->completedTimestampedQuizIds));

            return;
        }

        $this->completeCurrentModule();
    }

    protected function completeCurrentModule(): void
    {
        Progress::query()->updateOrCreate(
            ['user_id' => auth()->id(), 'content_id' => $this->currentModule->id],
            ['completed_at' => now()]
        );
    }

    public function finishMainQuiz(): void
    {
        $this->clearQuizState();
        $this->loadCourseData();
    }

    public function continueTimestampedQuiz(): void
    {
        if ($this->activeTimestampedQuizId) {
            $this->dispatch('timestamped-quiz-resolved', quizId: $this->activeTimestampedQuizId);
        }

        $this->clearQuizState();
    }

    public function toggleFeedback(): void
    {
        $this->showFeedback = ! $this->showFeedback;
    }

    public function togglePuzzle(): void
    {
        $this->showPuzzle = ! $this->showPuzzle;
    }

    public function postComment(): void
    {
        $validated = $this->validate([
            'newComment' => 'required|string|max:1000',
        ]);

        Comment::query()->create([
            'content_id' => $this->currentModule->id,
            'user_id' => auth()->id(),
            'comment_text' => $validated['newComment'],
        ]);

        $this->newComment = '';
        $this->loadComments();
    }

    public function postReply(int $commentId): void
    {
        $parentComment = Comment::query()
            ->where('content_id', $this->currentModule->id)
            ->findOrFail($commentId);

        $replyText = trim((string) ($this->replyDrafts[$commentId] ?? ''));

        validator(
            ['reply' => $replyText],
            ['reply' => 'required|string|max:1000']
        )->validate();

        Comment::query()->create([
            'content_id' => $this->currentModule->id,
            'parent_comment_id' => $parentComment->id,
            'user_id' => auth()->id(),
            'comment_text' => $replyText,
        ]);

        unset($this->replyDrafts[$commentId]);
        $this->activeReplyCommentId = null;
        $this->loadComments();
    }

    public function toggleReplyForm(int $commentId): void
    {
        $this->activeReplyCommentId = $this->activeReplyCommentId === $commentId ? null : $commentId;
    }

    public function render()
    {
        return view('livewire.faculty.course-player')->layout('layouts.app');
    }
}
