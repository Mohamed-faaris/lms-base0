<div>
    <div class="flex items-center gap-4 mb-6">
        <flux:button variant="ghost" href="{{ route('admin.courses.show', $endQuiz?->content?->module?->topic?->course?->id) }}" wire:navigate icon="arrow-left" />
        <div class="flex-1">
            <flux:heading level="1" size="xl">End Quiz</flux:heading>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                {{ $endQuiz?->content?->title ?? 'Quiz Details' }}
            </p>
        </div>
        @if ($endQuiz)
            <flux:button href="{{ route('admin.courses.end-quiz.edit', [$endQuiz->content->module->topic->course->id, $endQuiz->id]) }}" wire:navigate>
                Edit
            </flux:button>
        @endif
    </div>

    @if ($endQuiz)
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6 mb-6">
            <div class="flex items-center gap-2 mb-4">
                <flux:icon.clipboard-document-check class="w-5 h-5 text-green-500" />
                <flux:heading level="2" size="lg">End Quiz</flux:heading>
                <flux:badge color="green">End of Module</flux:badge>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Course</p>
                    <p class="text-zinc-600 dark:text-zinc-400">{{ $endQuiz->content->module->topic->course->title }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Module</p>
                    <p class="text-zinc-600 dark:text-zinc-400">{{ $endQuiz->content->module->title }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Content</p>
                    <p class="text-zinc-600 dark:text-zinc-400">{{ $endQuiz->content->title }}</p>
                </div>
            </div>
        </div>

        @if ($endQuiz->quiz && $endQuiz->quiz->questions->count() > 0)
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <div class="flex items-center gap-2 mb-4">
                    <flux:icon.question-mark-circle class="w-5 h-5 text-blue-500" />
                    <flux:heading level="2" size="lg">Questions</flux:heading>
                    <flux:badge color="blue">{{ $endQuiz->quiz->questions->count() }}</flux:badge>
                </div>

                <div class="space-y-6">
                    @foreach ($endQuiz->quiz->questions as $questionIndex => $question)
                        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Question {{ $questionIndex + 1 }}</span>
                                <flux:badge size="xs">{{ $question->type === 'multiple_choice' ? 'Multiple Choice' : 'True/False' }}</flux:badge>
                            </div>

                            <div class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg text-zinc-600 dark:text-zinc-400 mb-3">
                                {{ $question->question_text }}
                            </div>

                            @if ($question->options)
                                <div class="space-y-2">
                                    @foreach ($question->options as $index => $option)
                                        @php
                                            $isCorrect = in_array($index, $question->correct_answer ?? []);
                                        @endphp
                                        <div class="flex items-center gap-2 p-2 rounded-lg {{ $isCorrect ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-zinc-50 dark:bg-zinc-900' }}">
                                            <span class="w-7 h-7 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-sm font-medium {{ $isCorrect ? 'text-green-600' : 'text-zinc-600' }}">
                                                {{ chr(65 + $index) }}
                                            </span>
                                            <span class="text-sm {{ $isCorrect ? 'text-green-700 dark:text-green-300 font-medium' : 'text-zinc-600 dark:text-zinc-400' }}">
                                                {{ $option }}
                                            </span>
                                            @if ($isCorrect)
                                                <flux:icon.check-circle class="w-5 h-5 text-green-600 ml-auto" />
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <flux:badge color="red">No questions added</flux:badge>
                <p class="text-sm text-zinc-500 mt-2">Add questions to this quiz.</p>
            </div>
        @endif
    @else
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <p class="text-zinc-500">End quiz not found.</p>
        </div>
    @endif
</div>
