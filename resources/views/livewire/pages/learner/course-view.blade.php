<?php

use App\Enums\AttemptStatus;
use App\Enums\ProgressStatus;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseModule;
use App\Models\LearningProgress;
use App\Models\ModuleItem;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public Course $course;

    public CourseEnrollment $enrollment;

    public Collection $modules;

    public Collection $progress;

    public ?ModuleItem $selectedItem = null;

    public ?QuizAttempt $currentAttempt = null;

    public array $answers = [];

    public ?int $viewingAttemptId = null;

    public function mount(Course $course, $item = null): void
    {
        $this->course = $course;

        $this->enrollment = CourseEnrollment::with('courseVersion')
            ->where('student_id', auth()->id())
            ->whereHas('courseVersion', fn ($q) => $q->where('course_id', $course->id))
            ->firstOrFail();

        $this->modules = CourseModule::with([
            'items' => fn ($q) => $q->orderBy('sort_order'),
            'items.contentAsset',
            'items.quiz.questions.options',
        ])
            ->where('course_version_id', $this->enrollment->course_version_id)
            ->orderBy('sort_order')
            ->get();

        $this->progress = LearningProgress::with('videoSession')
            ->where('enrollment_id', $this->enrollment->id)
            ->get()
            ->keyBy('module_item_id');

        if ($item) {
            $itemModel = ModuleItem::with(['contentAsset', 'quiz.questions.options'])->find((int) $item);
            if ($itemModel && ! $this->isLocked($itemModel)) {
                $this->selectedItem = $itemModel;
            }
        }
    }

    public function allItems(): Collection
    {
        $items = collect();
        foreach ($this->modules as $module) {
            foreach ($module->items as $modItem) {
                $items->push($modItem);
            }
        }

        return $items;
    }

    public function isLocked(ModuleItem $item): bool
    {
        foreach ($this->allItems() as $modItem) {
            if ($modItem->id === $item->id) {
                return false;
            }
            if ($modItem->required) {
                $progress = $this->progress->get($modItem->id);
                if (! $progress || $progress->status !== ProgressStatus::COMPLETED) {
                    return true;
                }
            }
        }

        return false;
    }

    public function progressStatus(ModuleItem $item): string
    {
        $p = $this->progress->get($item->id);

        if (! $p) {
            return 'not_started';
        }

        return $p->status->value;
    }

    public function progressPercent(ModuleItem $item): float
    {
        $p = $this->progress->get($item->id);

        return $p ? (float) $p->progress : 0;
    }

    public function markVideoComplete(int $itemId, int $duration): void
    {
        logger('🎬 markVideoComplete called', ['itemId' => $itemId, 'duration' => $duration, 'user' => auth()->id()]);

        $progress = $this->progress->get($itemId);

        if (! $progress) {
            $progress = LearningProgress::create([
                'enrollment_id' => $this->enrollment->id,
                'module_item_id' => $itemId,
                'status' => ProgressStatus::COMPLETED,
                'progress' => 100,
                'started_at' => now(),
                'completed_at' => now(),
                'time_spent' => $duration,
            ]);
        } else {
            $progress->update([
                'status' => ProgressStatus::COMPLETED,
                'progress' => 100,
                'completed_at' => now(),
                'time_spent' => $duration,
            ]);
        }

        $progress->videoSession()->updateOrCreate(
            ['progress_id' => $progress->id],
            [
                'last_second' => $duration,
                'watched_seconds' => $duration,
                'watch_percentage' => 100,
            ],
        );

        $this->progress = LearningProgress::with('videoSession')
            ->where('enrollment_id', $this->enrollment->id)
            ->get()
            ->keyBy('module_item_id');

        logger('🎬 markVideoComplete DB save done', ['itemId' => $itemId, 'progressId' => $progress->id, 'status' => $progress->status->value]);
    }

    public function startQuiz(int $quizId): void
    {
        $quiz = Quiz::with('questions.options')->findOrFail($quizId);

        $attemptCount = QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', auth()->id())
            ->count();

        if ($attemptCount >= $quiz->attempt_limit) {
            session()->flash('error', 'No attempts remaining.');
            return;
        }

        $this->currentAttempt = QuizAttempt::create([
            'quiz_id' => $quizId,
            'student_id' => auth()->id(),
            'attempt_no' => $attemptCount + 1,
            'status' => AttemptStatus::STARTED,
            'started_at' => now(),
        ]);

        $this->answers = [];
        $this->viewingAttemptId = null;

        $quiz->load('questions');
        foreach ($quiz->questions as $q) {
            $this->answers[$q->id] = $q->type->value === 'multiple' ? [] : null;
        }

        $itemProgress = $this->progress->get($this->selectedItem->id);
        if (! $itemProgress) {
            $itemProgress = LearningProgress::create([
                'enrollment_id' => $this->enrollment->id,
                'module_item_id' => $this->selectedItem->id,
                'status' => ProgressStatus::STARTED,
                'progress' => 0,
                'started_at' => now(),
                'time_spent' => 0,
            ]);
            $this->progress->put($this->selectedItem->id, $itemProgress);
        }
    }

    public function submitQuiz(): void
    {
        if (! $this->currentAttempt) {
            return;
        }

        $quiz = Quiz::with('questions.options')->findOrFail($this->currentAttempt->quiz_id);

        $totalMarks = 0;
        $earnedMarks = 0;

        foreach ($quiz->questions as $question) {
            $answer = $this->answers[$question->id] ?? null;

            $isCorrect = null;
            $marks = 0;

            if ($answer !== null && $answer !== '') {
                $isCorrect = $this->gradeQuestion($question, $answer);

                if ($isCorrect === true) {
                    $marks = $question->marks;
                } elseif ($isCorrect === false) {
                    $marks = 0;
                } else {
                    $marks = 0;
                }

                if ($isCorrect === true) {
                    $earnedMarks += $marks;
                }
            }

            $totalMarks += $question->marks;

            QuizAnswer::create([
                'attempt_id' => $this->currentAttempt->id,
                'question_id' => $question->id,
                'answer' => is_array($answer) ? json_encode($answer) : (string) $answer,
                'is_correct' => $isCorrect,
                'marks' => $marks,
            ]);
        }

        $score = $totalMarks > 0 ? round(($earnedMarks / $totalMarks) * 100) : 0;

        $this->currentAttempt->update([
            'score' => $score,
            'status' => AttemptStatus::SUBMITTED,
            'submitted_at' => now(),
        ]);

        $this->viewingAttemptId = $this->currentAttempt->id;
        $this->currentAttempt->load('answers.question.options');

        $itemProgress = $this->progress->get($this->selectedItem->id);
        if ($itemProgress) {
            $itemProgress->update([
                'status' => ProgressStatus::COMPLETED,
                'progress' => 100,
                'completed_at' => now(),
                'time_spent' => $quiz->duration * 60,
                'score' => $score,
            ]);
        }

        $this->progress = LearningProgress::with('videoSession')
            ->where('enrollment_id', $this->enrollment->id)
            ->get()
            ->keyBy('module_item_id');

        $this->currentAttempt = null;
    }

    private function gradeQuestion(Question $question, mixed $answer): ?bool
    {
        $type = $question->type->value;

        if ($type === 'subjective') {
            return null;
        }

        $correctOptions = $question->options->where('is_correct', true);

        if ($type === 'fill_blank') {
            $normalized = strtolower(trim((string) $answer));
            foreach ($correctOptions as $opt) {
                if (strtolower(trim($opt->option_text)) === $normalized) {
                    return true;
                }
            }
            return false;
        }

        if ($type === 'mcq' || $type === 'true_false') {
            $selectedId = (int) $answer;
            $selected = $question->options->firstWhere('id', $selectedId);
            return $selected && $selected->is_correct;
        }

        if ($type === 'multiple') {
            $selectedIds = (array) $answer;
            $correctIds = $correctOptions->pluck('id')->sort()->values()->toArray();
            $userIds = collect($selectedIds)->map(fn ($v) => (int) $v)->sort()->values()->toArray();
            return $correctIds === $userIds;
        }

        return false;
    }

    public function viewAttempt(int $attemptId): void
    {
        $attempt = QuizAttempt::with('answers.question.options')
            ->where('id', $attemptId)
            ->where('student_id', auth()->id())
            ->first();

        if ($attempt) {
            $this->viewingAttemptId = $attempt->id;
            $this->currentAttempt = null;
            $this->answers = [];
        }
    }

    public function closeResults(): void
    {
        $this->viewingAttemptId = null;
        $this->currentAttempt = null;
        $this->answers = [];
    }

    public function getPreviousItem(): ?ModuleItem
    {
        $items = $this->allItems();
        $ids = $items->pluck('id')->values()->toArray();
        $idx = array_search($this->selectedItem?->id, $ids);

        if ($idx === false || $idx === 0) {
            return null;
        }

        $prev = $items[$idx - 1];

        return $this->isLocked($prev) ? null : $prev;
    }

    public function getNextItem(): ?ModuleItem
    {
        $items = $this->allItems();
        $ids = $items->pluck('id')->values()->toArray();
        $idx = array_search($this->selectedItem?->id, $ids);

        if ($idx === false) {
            return null;
        }

        for ($i = $idx + 1; $i < count($ids); $i++) {
            $candidate = $items[$i];
            if (! $this->isLocked($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    public function getPastAttemptsProperty(): Collection
    {
        if (! $this->selectedItem || ! $this->selectedItem->quiz) {
            return collect();
        }

        return QuizAttempt::with('answers')
            ->where('quiz_id', $this->selectedItem->quiz->id)
            ->where('student_id', auth()->id())
            ->whereIn('status', [AttemptStatus::SUBMITTED, AttemptStatus::GRADED])
            ->orderByDesc('created_at')
            ->get();
    }
}; ?>

<div class="flex h-[calc(100vh-4rem)]">
    <aside class="w-72 flex-shrink-0 bg-white border-r border-gray-200 overflow-y-auto">
        <div class="p-4">
            <a href="{{ route('learner.my-learning.course', $course->slug) }}" wire:navigate
               class="block font-semibold text-sm text-gray-900 mb-4 hover:text-indigo-600 transition">
                {{ $course->title }}
            </a>

            <div class="space-y-1">
                <a href="{{ route('learner.my-learning.course', $course->slug) }}" wire:navigate
                   class="flex items-center gap-2 px-2 py-1.5 text-sm rounded-md {{ request()->route('item') ? 'text-gray-600 hover:bg-gray-100' : 'bg-indigo-50 text-indigo-700 font-medium' }}">
                    <x-lucide-layout-dashboard class="w-4 h-4" />
                    Overview
                </a>
            </div>

            <hr class="my-3 border-gray-200">

            @foreach ($modules as $module)
                <div class="mb-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 mb-1">
                        {{ $module->title }}
                    </p>

                    <div class="space-y-0.5">
                        @foreach ($module->items as $item)
                            @php
                                $itemStatus = $this->progressStatus($item);
                                $locked = $this->isLocked($item);
                                $isActive = request()->route('item') == $item->id;
                            @endphp

                            @if ($locked)
                                <div class="flex items-center gap-2 px-2 py-1.5 text-sm text-gray-400 cursor-not-allowed">
                                    <x-lucide-lock class="w-4 h-4 flex-shrink-0" />
                                    <span class="truncate">{{ $item->title }}</span>
                                </div>
                            @else
                                <a href="{{ route('learner.my-learning.course', [$course->slug, 'item' => $item->id]) }}"
                                   wire:navigate
                                   class="flex items-center gap-2 px-2 py-1.5 text-sm rounded-md {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                                    @if ($itemStatus === 'completed')
                                        <x-lucide-check-circle class="w-4 h-4 flex-shrink-0 text-green-500" />
                                    @elseif ($itemStatus === 'started' || $itemStatus === 'in_progress')
                                        <x-lucide-play-circle class="w-4 h-4 flex-shrink-0 text-amber-500" />
                                    @else
                                        <x-lucide-circle class="w-4 h-4 flex-shrink-0 text-gray-300" />
                                    @endif
                                    <span class="truncate">{{ $item->title }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach

            <hr class="my-3 border-gray-200">

            <div class="space-y-1">
                <div class="flex items-center gap-2 px-2 py-1.5 text-sm text-gray-400 cursor-not-allowed">
                    <x-lucide-award class="w-4 h-4" />
                    Certificate
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto bg-gray-50">
        @if ($selectedItem)
            <div class="max-w-4xl mx-auto p-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    @switch($selectedItem->type->value)
                        @case('video')
                            @php $asset = $selectedItem->contentAsset; @endphp

                            @if ($asset && $asset->storage->value === 'youtube' && data_get($asset, 'metadata.youtube_id'))
                                @php
                                    $vidProgress = $this->progress->get($selectedItem->id);
                                    $lastSecond = $vidProgress && $vidProgress->videoSession
                                        ? $vidProgress->videoSession->last_second
                                        : 0;
                                @endphp

                                <div
                                    x-data="youtubePlayer('{{ $asset->metadata['youtube_id'] }}', {{ $lastSecond }}, {{ $selectedItem->id }})"
                                >
                                    <div class="aspect-video bg-black max-h-[55vh]">
                                        <div id="yt-player" class="w-full h-full"></div>
                                    </div>

                                    <div class="p-4 sm:p-6">
                                        <h1 class="text-lg font-semibold text-gray-900 mb-3">{{ $selectedItem->title }}</h1>

                                        <div class="space-y-1">
                                            <div
                                                class="relative h-1.5 bg-gray-100 rounded-full overflow-hidden group" data-timeline
                                            >
                                                <div
                                                    class="absolute inset-y-0 left-0 bg-indigo-300 rounded-full"
                                                    :style="`width: ${backendPercent}%`"
                                                ></div>
                                                <div
                                                    class="absolute inset-y-0 left-0 bg-blue-500 rounded-full transition-all duration-150"
                                                    :style="`width: ${progressPercent}%`"
                                                ></div>
                                                <div
                                                    class="absolute top-0 bottom-0 w-0.5 bg-gray-400/60 z-10"
                                                    :style="`left: ${maxSeekPercent}%`"
                                                    x-show="!completed && maxSeek < duration"
                                                ></div>
                                                <div
                                                    class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-white/20 rounded-full transition-opacity pointer-events-none"
                                                ></div>
                                                <div
                                                    class="absolute inset-y-0 left-0 z-20"
                                                    :style="`right: ${100 - maxSeekPercent}%`"
                                                    x-show="!completed"
                                                    @mousedown="seekStart($event)"
                                                ></div>
                                                <div
                                                    class="absolute inset-y-0 right-0 z-20 cursor-not-allowed rounded-r-full"
                                                    :style="`left: ${maxSeekPercent}%`"
                                                    x-show="!completed && maxSeek < duration"
                                                >
                                                    <div class="absolute inset-0 bg-gray-300/40 rounded-r-full"></div>
                                                </div>
                                                <div
                                                    class="absolute inset-0 z-20 cursor-pointer"
                                                    x-show="completed"
                                                    @mousedown="seekStart($event)"
                                                ></div>
                                                <template x-if="currentTime > 0">
                                                    <div
                                                        class="absolute top-1/2 -translate-y-1/2 w-3 h-3 bg-blue-600 border-2 border-white rounded-full shadow-md pointer-events-none"
                                                        :style="`left: calc(${progressPercent}% - 6px)`"
                                                    ></div>
                                                </template>
                                            </div>
                                            <div class="flex items-center justify-between text-xs text-gray-500">
                                                <span x-text="formattedTime"></span>
                                                <span x-text="formattedDuration"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($asset)
                                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg mb-4">
                                        <x-lucide-video class="w-8 h-8 text-indigo-500" />
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $asset->title }}</p>
                                            <p class="text-sm text-gray-500">
                                                <a href="{{ $asset->path }}" target="_blank" class="text-indigo-600 hover:underline">Open video</a>
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <div class="prose prose-sm max-w-none text-gray-600 mt-4">
                                    <p>Watch the video to learn about this topic.</p>
                                </div>
                            </div>
                            @break

                        @case('pdf')
                            @php $asset = $selectedItem->contentAsset; @endphp
                            <div class="p-6">
                                <h1 class="text-xl font-semibold text-gray-900 mb-4">{{ $selectedItem->title }}</h1>

                                @if ($asset)
                                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg mb-4">
                                        <x-lucide-file-text class="w-8 h-8 text-red-500" />
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 truncate">{{ $asset->title }}</p>
                                            @if (data_get($asset, 'metadata.file_size'))
                                                <p class="text-sm text-gray-500">
                                                    {{ round($asset->metadata['file_size'] / 1024) }} KB
                                                    &middot;
                                                    {{ data_get($asset, 'metadata.pages', '?') }} pages
                                                </p>
                                            @endif
                                        </div>
                                        <a href="{{ $asset->path }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-indigo-700 bg-indigo-50 rounded-md hover:bg-indigo-100 transition">
                                            <x-lucide-download class="w-4 h-4" />
                                            Download
                                        </a>
                                    </div>
                                @endif

                                <div class="prose prose-sm max-w-none text-gray-600">
                                    <p>Download and review the PDF document above for reference material on this topic.</p>
                                </div>
                            </div>
                            @break

                        @case('quiz')
                            @php $quiz = $selectedItem->quiz; @endphp

                            @if (! $quiz)
                                <div class="p-6">
                                    <p class="text-gray-500">Quiz not configured.</p>
                                </div>
                            @elseif ($viewingAttemptId)
                                @php
                                    $attempt = QuizAttempt::with('answers.question.options')
                                        ->where('student_id', auth()->id())->find($viewingAttemptId);
                                @endphp
                                <div class="p-6">
                                    <h1 class="text-xl font-semibold text-gray-900 mb-4">{{ $selectedItem->title }}</h1>

                                    <div class="mb-6 p-4 {{ ($attempt->score ?? 0) >= $quiz->passing_marks ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} border rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-lg font-bold {{ ($attempt->score ?? 0) >= $quiz->passing_marks ? 'text-green-700' : 'text-red-700' }}">
                                                    {{ $attempt->score ?? 0 }}%
                                                </p>
                                                <p class="text-sm {{ ($attempt->score ?? 0) >= $quiz->passing_marks ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ ($attempt->score ?? 0) >= $quiz->passing_marks ? 'Passed' : 'Did not pass' }}
                                                </p>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Attempt #{{ $attempt->attempt_no }} &middot;
                                                {{ $attempt->submitted_at?->diffForHumans() ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-6">
                                        @foreach ($attempt->answers as $answer)
                                            @php $q = $answer->question; @endphp
                                            <div class="border rounded-lg p-4 {{ $answer->is_correct === true ? 'border-green-200 bg-green-50/50' : ($answer->is_correct === false ? 'border-red-200 bg-red-50/50' : 'border-gray-200') }}">
                                                <div class="flex items-start justify-between gap-2 mb-2">
                                                    <p class="font-medium text-gray-900">{{ $q->question }}</p>
                                                    <span class="text-xs font-medium px-2 py-0.5 rounded {{ $answer->is_correct === true ? 'bg-green-100 text-green-700' : ($answer->is_correct === false ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500') }}">
                                                        {{ $answer->is_correct === true ? '+' . $q->marks : ($answer->is_correct === false ? '0' : '?') }}/{{ $q->marks }}
                                                    </span>
                                                </div>

                                                @if ($q->type->value === 'mcq' || $q->type->value === 'true_false')
                                                    <div class="space-y-1 text-sm">
                                                        @foreach ($q->options as $opt)
                                                            @php
                                                                $wasSelected = (int) $answer->answer === $opt->id;
                                                            @endphp
                                                            <div class="flex items-center gap-2 px-3 py-1.5 rounded {{ $opt->is_correct ? 'bg-green-100 text-green-800' : ($wasSelected ? 'bg-red-100 text-red-800' : 'text-gray-500') }}">
                                                                @if ($opt->is_correct)
                                                                    <x-lucide-check-circle class="w-4 h-4 text-green-600 flex-shrink-0" />
                                                                @elseif ($wasSelected)
                                                                    <x-lucide-x-circle class="w-4 h-4 text-red-600 flex-shrink-0" />
                                                                @else
                                                                    <span class="w-4 h-4 flex-shrink-0"></span>
                                                                @endif
                                                                <span>{{ $opt->option_text }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @elseif ($q->type->value === 'multiple')
                                                    @php
                                                        $selectedIds = json_decode($answer->answer ?? '[]', true);
                                                    @endphp
                                                    <div class="space-y-1 text-sm">
                                                        @foreach ($q->options as $opt)
                                                            @php
                                                                $wasSelected = in_array((int) $opt->id, $selectedIds);
                                                            @endphp
                                                            <div class="flex items-center gap-2 px-3 py-1.5 rounded {{ $opt->is_correct ? 'bg-green-100 text-green-800' : ($wasSelected ? 'bg-red-100 text-red-800' : 'text-gray-500') }}">
                                                                @if ($opt->is_correct)
                                                                    <x-lucide-check-circle class="w-4 h-4 text-green-600 flex-shrink-0" />
                                                                @elseif ($wasSelected)
                                                                    <x-lucide-x-circle class="w-4 h-4 text-red-600 flex-shrink-0" />
                                                                @else
                                                                    <span class="w-4 h-4 flex-shrink-0"></span>
                                                                @endif
                                                                <span>{{ $opt->option_text }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @elseif ($q->type->value === 'fill_blank' || $q->type->value === 'subjective')
                                                    <div class="text-sm space-y-1">
                                                        <p class="text-gray-500">Your answer: <span class="font-medium text-gray-900">{{ $answer->answer }}</span></p>
                                                        @if ($q->type->value === 'fill_blank' && $q->options->count())
                                                            <p class="text-gray-500">Correct: <span class="font-medium text-green-700">{{ $q->options->firstWhere('is_correct', true)?->option_text ?? 'N/A' }}</span></p>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if ($q->explanation)
                                                    <p class="mt-2 text-xs text-gray-500 italic">{{ $q->explanation }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-6 flex gap-3">
                                        @php
                                            $pastAttemptsCount = QuizAttempt::where('quiz_id', $quiz->id)
                                                ->where('student_id', auth()->id())
                                                ->whereIn('status', [AttemptStatus::SUBMITTED, AttemptStatus::GRADED])
                                                ->count();
                                        @endphp
                                        @if ($pastAttemptsCount < $quiz->attempt_limit)
                                            <button wire:click="closeResults" class="px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                                                Retry Quiz
                                            </button>
                                        @endif
                                        <a href="{{ route('learner.my-learning.course', $course->slug) }}" wire:navigate
                                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                            Back to Overview
                                        </a>
                                    </div>
                                </div>
                            @elseif ($currentAttempt && $currentAttempt->quiz_id === $quiz->id)
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-6">
                                        <h1 class="text-xl font-semibold text-gray-900">{{ $selectedItem->title }}</h1>
                                        <span class="text-sm text-gray-500">Attempt #{{ $currentAttempt->attempt_no }}</span>
                                    </div>

                                    <div class="space-y-6">
                                        @foreach ($quiz->questions as $question)
                                            <div class="border border-gray-200 rounded-lg p-4">
                                                <p class="font-medium text-gray-900 mb-3">{{ $question->question }}
                                                    <span class="text-xs text-gray-400 font-normal">({{ $question->marks }} mark{{ $question->marks !== 1 ? 's' : '' }})</span>
                                                </p>

                                                @if ($question->type->value === 'mcq' || $question->type->value === 'true_false')
                                                    <div class="space-y-2">
                                                        @foreach ($question->options as $opt)
                                                            <label class="flex items-center gap-3 px-3 py-2 rounded-lg border cursor-pointer transition {{ isset($answers[$question->id]) && (int) $answers[$question->id] === $opt->id ? 'border-indigo-300 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}">
                                                                <input type="radio" name="q_{{ $question->id }}" value="{{ $opt->id }}"
                                                                    wire:model.live="answers.{{ $question->id }}"
                                                                    class="text-indigo-600 focus:ring-indigo-500">
                                                                <span class="text-sm text-gray-700">{{ $opt->option_text }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @elseif ($question->type->value === 'multiple')
                                                    <div class="space-y-2">
                                                        @foreach ($question->options as $opt)
                                                            <label class="flex items-center gap-3 px-3 py-2 rounded-lg border cursor-pointer transition {{ isset($answers[$question->id]) && in_array((string) $opt->id, (array) $answers[$question->id]) ? 'border-indigo-300 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}">
                                                                <input type="checkbox" value="{{ $opt->id }}"
                                                                    wire:model.live="answers.{{ $question->id }}"
                                                                    class="text-indigo-600 focus:ring-indigo-500 rounded">
                                                                <span class="text-sm text-gray-700">{{ $opt->option_text }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @elseif ($question->type->value === 'fill_blank')
                                                    <input type="text"
                                                        wire:model.live="answers.{{ $question->id }}"
                                                        placeholder="Type your answer..."
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                                @elseif ($question->type->value === 'subjective')
                                                    <textarea
                                                        wire:model.live="answers.{{ $question->id }}"
                                                        rows="4"
                                                        placeholder="Write your answer..."
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                                    ></textarea>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-6 flex gap-3">
                                        <button wire:click="submitQuiz" wire:loading.attr="disabled"
                                            class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50">
                                            <span wire:loading.remove wire:target="submitQuiz">Submit Answers</span>
                                            <span wire:loading wire:target="submitQuiz">Submitting...</span>
                                        </button>
                                        <button wire:click="closeResults"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="p-6">
                                    <h1 class="text-xl font-semibold text-gray-900 mb-4">{{ $selectedItem->title }}</h1>

                                    <div class="space-y-4">
                                        <div class="flex items-center gap-4 text-sm text-gray-600">
                                            <span class="flex items-center gap-1">
                                                <x-lucide-help-circle class="w-4 h-4" />
                                                {{ $quiz->questions->count() }} questions
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <x-lucide-clock class="w-4 h-4" />
                                                {{ $quiz->duration }} min
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <x-lucide-target class="w-4 h-4" />
                                                Pass: {{ $quiz->passing_marks }}/{{ $quiz->questions->sum('marks') }}
                                            </span>
                                        </div>

                                        @php
                                            $pastAttempts = $this->getPastAttemptsProperty();
                                            $remaining = $quiz->attempt_limit - $pastAttempts->count();
                                        @endphp

                                        @if ($pastAttempts->isNotEmpty())
                                            <div class="space-y-2">
                                                <p class="text-sm font-medium text-gray-700">Previous Attempts</p>
                                                @foreach ($pastAttempts as $pa)
                                                    <button wire:click="viewAttempt({{ $pa->id }})"
                                                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition text-sm">
                                                        <span class="font-medium">{{ $pa->score ?? '?' }}%</span>
                                                        <span class="text-gray-500">Attempt #{{ $pa->attempt_no }} &middot; {{ $pa->submitted_at?->diffForHumans() }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if ($remaining > 0)
                                            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                                <div class="flex items-start gap-3">
                                                    <x-lucide-play-circle class="w-5 h-5 text-indigo-500 flex-shrink-0 mt-0.5" />
                                                    <div class="text-sm text-indigo-800">
                                                        <p class="font-medium">Ready to start?</p>
                                                        <p class="mt-1">You have {{ $remaining }} attempt{{ $remaining !== 1 ? 's' : '' }} remaining. Duration: {{ $quiz->duration }} minutes. You need at least {{ $quiz->passing_marks }} points to pass.</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <button wire:click="startQuiz({{ $quiz->id }})" wire:loading.attr="disabled"
                                                class="w-full px-4 py-3 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50">
                                                <span wire:loading.remove wire:target="startQuiz({{ $quiz->id }})">Start Quiz</span>
                                                <span wire:loading wire:target="startQuiz({{ $quiz->id }})">Starting...</span>
                                            </button>
                                        @else
                                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                                <div class="flex items-start gap-3">
                                                    <x-lucide-alert-triangle class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" />
                                                    <div class="text-sm text-gray-600">
                                                        <p class="font-medium">No attempts remaining</p>
                                                        <p class="mt-1">You have used all {{ $quiz->attempt_limit }} attempt(s).</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @break

                        @case('survey')
                            <div class="p-6">
                                <h1 class="text-xl font-semibold text-gray-900 mb-4">{{ $selectedItem->title }}</h1>
                                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                                    <x-lucide-clipboard-check class="w-8 h-8 text-purple-500" />
                                    <div>
                                        <p class="font-medium text-gray-900">Course Experience Survey</p>
                                        <p class="text-sm text-gray-500">Share your feedback on the course content and structure.</p>
                                    </div>
                                </div>
                                <div class="prose prose-sm max-w-none text-gray-600 mt-4">
                                    <p>Your feedback helps us improve the learning experience. This survey is anonymous and should take about 5 minutes to complete.</p>
                                </div>
                            </div>
                            @break

                        @case('external_link')
                            @php $link = data_get($selectedItem, 'settings.url', '#'); @endphp
                            <div class="p-6">
                                <h1 class="text-xl font-semibold text-gray-900 mb-4">{{ $selectedItem->title }}</h1>
                                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                                    <x-lucide-external-link class="w-8 h-8 text-sky-500" />
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate">{{ parse_url($link, PHP_URL_HOST) ?: 'External Link' }}</p>
                                        <p class="text-sm text-gray-500 truncate">{{ $link }}</p>
                                    </div>
                                    <a href="{{ $link }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-sky-700 bg-sky-50 rounded-md hover:bg-sky-100 transition">
                                        Visit
                                        <x-lucide-external-link class="w-3.5 h-3.5" />
                                    </a>
                                </div>
                            </div>
                            @break

                        @case('custom_page')
                            <div class="p-6">
                                <h1 class="text-xl font-semibold text-gray-900 mb-4">{{ $selectedItem->title }}</h1>
                                <div class="prose prose-sm max-w-none text-gray-600">
                                    @if ($content = data_get($selectedItem, 'settings.content'))
                                        {!! $content !!}
                                    @else
                                        <p>This page contains custom content for this topic.</p>
                                    @endif
                                </div>
                            </div>
                            @break

                        @default
                            <div class="p-6">
                                <h1 class="text-xl font-semibold text-gray-900 mb-4">{{ $selectedItem->title }}</h1>
                                <p class="text-gray-500">Content type not supported yet.</p>
                            </div>
                    @endswitch
                </div>

                @php
                    $prevItem = $this->getPreviousItem();
                    $nextItem = $this->getNextItem();
                @endphp

                <div class="flex items-center justify-between mt-4">
                    @if ($prevItem)
                        <a href="{{ route('learner.my-learning.course', [$course->slug, 'item' => $prevItem->id]) }}"
                           wire:navigate
                           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <x-lucide-chevron-left class="w-4 h-4" />
                            {{ $prevItem->title }}
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if ($nextItem)
                        <a href="{{ route('learner.my-learning.course', [$course->slug, 'item' => $nextItem->id]) }}"
                           wire:navigate
                           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                            {{ $nextItem->title }}
                            <x-lucide-chevron-right class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </div>
        @else
            <div class="max-w-4xl mx-auto p-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $course->title }}</h1>
                    <p class="text-gray-600">{{ $course->description }}</p>

                    <div class="mt-6 grid grid-cols-3 gap-4">
                        <div class="bg-indigo-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-indigo-700">{{ $modules->count() }}</p>
                            <p class="text-sm text-indigo-600">Modules</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-green-700">{{ $progress->where('status', ProgressStatus::COMPLETED)->count() }}</p>
                            <p class="text-sm text-green-600">Completed</p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-amber-700">{{ $this->allItems()->count() }}</p>
                            <p class="text-sm text-amber-600">Total Items</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Course Content</h2>

                    @foreach ($modules as $module)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="p-4 border-b border-gray-100">
                                <h3 class="font-medium text-gray-900">{{ $module->title }}</h3>
                                @if ($module->description)
                                    <p class="text-sm text-gray-500 mt-1">{{ $module->description }}</p>
                                @endif
                            </div>
                            <div class="divide-y divide-gray-100">
                                @foreach ($module->items as $item)
                                    @php
                                        $itemStatus = $this->progressStatus($item);
                                        $locked = $this->isLocked($item);
                                    @endphp
                                    @if ($locked)
                                        <div class="flex items-center gap-3 px-4 py-3 text-sm text-gray-400 cursor-not-allowed">
                                            <x-lucide-lock class="w-4 h-4 flex-shrink-0" />
                                            <span class="flex-1">{{ $item->title }}</span>
                                            <span class="text-xs capitalize">{{ str_replace('_', ' ', $item->type->value) }}</span>
                                        </div>
                                    @else
                                        <a href="{{ route('learner.my-learning.course', [$course->slug, 'item' => $item->id]) }}"
                                           wire:navigate
                                           class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-gray-50 transition">
                                            @if ($itemStatus === 'completed')
                                                <x-lucide-check-circle class="w-4 h-4 flex-shrink-0 text-green-500" />
                                            @elseif ($itemStatus === 'started' || $itemStatus === 'in_progress')
                                                <x-lucide-play-circle class="w-4 h-4 flex-shrink-0 text-amber-500" />
                                            @else
                                                <x-lucide-circle class="w-4 h-4 flex-shrink-0 text-gray-300" />
                                            @endif
                                            <span class="flex-1 text-gray-900">{{ $item->title }}</span>
                                            <span class="text-xs text-gray-400 capitalize">{{ str_replace('_', ' ', $item->type->value) }}</span>
                                            <x-lucide-chevron-right class="w-4 h-4 text-gray-400" />
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </main>
</div>
