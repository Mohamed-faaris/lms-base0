<div>
    <div class="flex items-center gap-4 mb-6">
        <flux:button variant="ghost" href="{{ route('admin.courses.show', $content?->module?->topic?->course?->id) }}" wire:navigate icon="arrow-left" />
        <div class="flex-1">
            <flux:heading level="1" size="xl">{{ $content->title }}</flux:heading>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                {{ $content->module?->title ?? 'Content Details' }}
            </p>
        </div>
        <div class="flex gap-2">
            <flux:button href="{{ route('admin.courses.content.edit', [$content->module?->topic?->course?->id, $content->id]) }}" wire:navigate>
                Edit Content
            </flux:button>
            @if ($content->type->value === 'quiz')
                <flux:button variant="outline" href="{{ route('admin.courses.content.quiz.edit', [$content->module?->topic?->course?->id, $content->id]) }}" wire:navigate>
                    Edit Quiz
                </flux:button>
            @endif
        </div>
    </div>

    @if ($content)
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6 mb-6">
            <div class="flex items-center gap-2 mb-4">
                @switch($content->type->value)
                    @case('video')
                        <flux:icon.video-camera class="w-5 h-5 text-blue-500" />
                        @break
                    @case('quiz')
                        <flux:icon.clipboard-document-check class="w-5 h-5 text-green-500" />
                        @break
                    @case('article')
                        <flux:icon.document class="w-5 h-5 text-purple-500" />
                        @break
                    @default
                        <flux:icon.document-text class="w-5 h-5 text-zinc-500" />
                @endswitch
                <flux:heading level="2" size="lg">{{ $content->type->label() }}</flux:heading>
                <flux:badge>{{ $content->type->value }}</flux:badge>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Course</p>
                    <p class="text-zinc-600 dark:text-zinc-400">{{ $content->module?->topic?->course?->title }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Module</p>
                    <p class="text-zinc-600 dark:text-zinc-400">{{ $content->module?->title }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Order</p>
                    <p class="text-zinc-600 dark:text-zinc-400">{{ $content->order }}</p>
                </div>

                @if ($content->content_url)
                    <div>
                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">URL</p>
                        <a href="{{ $content->content_url }}" target="_blank" class="text-sm text-blue-600 hover:underline">
                            {{ $content->content_url }}
                        </a>
                    </div>
                @endif

                @if ($content->body)
                    <div>
                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Body</p>
                        <div class="mt-1 p-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $content->body }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Video: Timestamp Quizzes Section --}}
        @if ($content->type->value === 'video')
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <flux:icon.clock class="w-5 h-5 text-blue-500" />
                        <flux:heading level="2" size="lg">Timestamp Quizzes</flux:heading>
                        <flux:badge color="blue">{{ $content->timestampedQuizzes->count() }}</flux:badge>
                    </div>
                    <flux:button size="sm" href="{{ route('admin.courses.show', $content->module->topic->course->id) }}" wire:navigate>
                        Manage
                    </flux:button>
                </div>
                @if ($content->timestampedQuizzes->count() > 0)
                    <div class="space-y-2">
                        @foreach ($content->timestampedQuizzes as $tsQuiz)
                            <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <flux:icon.video-camera class="w-4 h-4 text-blue-500" />
                                    <span class="text-sm font-mono">{{ gmdate('H:i:s', $tsQuiz->timestamp_seconds ?? 0) }}</span>
                                    <flux:badge size="xs">{{ $tsQuiz->questions->count() }} questions</flux:badge>
                                    <flux:badge size="xs" color="amber">Pass {{ $tsQuiz->passingScore() }}%</flux:badge>
                                </div>
                                <flux:button size="xs" variant="ghost" href="{{ route('admin.courses.content.timestamped-quiz.edit', [$content->module->topic->course->id, $content->id, $tsQuiz->id]) }}" wire:navigate>
                                    Edit
                                </flux:button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-zinc-500">No timestamp quizzes yet.</p>
                @endif
            </div>
        @endif

        {{-- Video: End Quiz Section --}}
        @if ($content->endQuiz)
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <flux:icon.clipboard-document-check class="w-5 h-5 text-green-500" />
                        <flux:heading level="2" size="lg">End Quiz</flux:heading>
                        <flux:badge color="green">End of Video</flux:badge>
                        <flux:badge color="amber">Pass {{ $content->endQuiz->passingScore() }}%</flux:badge>
                    </div>
                    <flux:button size="sm" href="{{ route('admin.courses.content.end-quiz.edit', [$content->module->topic->course->id, $content->id]) }}" wire:navigate>
                        Edit
                    </flux:button>
                </div>
                @if ($content->endQuiz->questions->count() > 0)
                    <div class="space-y-4">
                        @foreach ($content->endQuiz->questions as $questionIndex => $question)
                            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Question {{ $questionIndex + 1 }}</span>
                                    <flux:badge size="xs">{{ $question->type === 'multiple_choice' ? 'Multiple Choice' : 'True/False' }}</flux:badge>
                                </div>
                                <div class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $question->question_text }}
                                </div>
                                @if ($question->options)
                                    <div class="mt-3 space-y-2">
                                        @foreach ($question->options as $index => $option)
                                            @php
                                                $isCorrect = in_array($index, $question->correct_answer ?? []);
                                            @endphp
                                            <div class="flex items-center gap-2 p-2 rounded-lg {{ $isCorrect ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-zinc-50 dark:bg-zinc-900' }}">
                                                <span class="w-6 h-6 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-xs font-medium {{ $isCorrect ? 'text-green-600' : 'text-zinc-600' }}">
                                                    {{ chr(65 + $index) }}
                                                </span>
                                                <span class="text-sm {{ $isCorrect ? 'text-green-700 dark:text-green-300 font-medium' : 'text-zinc-600 dark:text-zinc-400' }}">
                                                    {{ $option }}
                                                </span>
                                                @if ($isCorrect)
                                                    <flux:icon.check-circle class="w-4 h-4 text-green-600 ml-auto" />
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-zinc-500">No question added yet.</p>
                @endif
            </div>
        @endif

        {{-- Quiz: Question Section --}}
        @if ($content->type->value === 'quiz' && $content->quiz)
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <flux:icon.clipboard-document-list class="w-5 h-5 text-green-500" />
                        <flux:heading level="2" size="lg">Quiz Questions</flux:heading>
                        <flux:badge color="amber">Pass {{ $content->quiz->passingScore() }}%</flux:badge>
                    </div>
                </div>
                @if ($content->quiz->questions->count() > 0)
                    <div class="space-y-4">
                        @foreach ($content->quiz->questions as $questionIndex => $question)
                            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Question {{ $questionIndex + 1 }}</span>
                                    <flux:badge size="xs">{{ $question->type === 'multiple_choice' ? 'Multiple Choice' : 'True/False' }}</flux:badge>
                                </div>
                                <div class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $question->question_text }}
                                </div>
                                @if ($question->options)
                                    <div class="mt-3 space-y-2">
                                        @foreach ($question->options as $index => $option)
                                            @php
                                                $isCorrect = in_array($index, $question->correct_answer ?? []);
                                            @endphp
                                            <div class="flex items-center gap-2 p-2 rounded-lg {{ $isCorrect ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-zinc-50 dark:bg-zinc-900' }}">
                                                <span class="w-6 h-6 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-xs font-medium {{ $isCorrect ? 'text-green-600' : 'text-zinc-600' }}">
                                                    {{ chr(65 + $index) }}
                                                </span>
                                                <span class="text-sm {{ $isCorrect ? 'text-green-700 dark:text-green-300 font-medium' : 'text-zinc-600 dark:text-zinc-400' }}">
                                                    {{ $option }}
                                                </span>
                                                @if ($isCorrect)
                                                    <flux:icon.check-circle class="w-4 h-4 text-green-600 ml-auto" />
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-zinc-500">No questions added yet.</p>
                @endif
            </div>
        @endif
    @else
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <p class="text-zinc-500">Content not found.</p>
        </div>
    @endif
</div>
