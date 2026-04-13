@if (! $quizSubmitted)
    <div class="space-y-6">
        @foreach ($quizQuestions as $questionIndex => $question)
            <div wire:key="quiz-question-{{ $question['id'] }}" class="space-y-3">
                <p class="font-medium text-zinc-950 dark:text-white">
                    {{ $questionIndex + 1 }}. {{ $question['question'] }}
                </p>
                <div class="space-y-2">
                    @foreach ($question['options'] as $option)
                        <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-zinc-200 p-4 transition hover:border-blue-300 hover:bg-blue-50/70 dark:border-zinc-800 dark:hover:border-blue-800 dark:hover:bg-zinc-800/80">
                            <input
                                type="radio"
                                name="q_{{ $question['id'] }}"
                                value="{{ $option['id'] }}"
                                wire:click="setAnswer('{{ $question['id'] }}', '{{ $option['id'] }}')"
                                @checked(isset($quizAnswers[$question['id']]) && $quizAnswers[$question['id']] === $option['id'])
                                class="h-4 w-4 border-zinc-300 text-blue-600 focus:ring-blue-500"
                            >
                            <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $option['text'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        <flux:button variant="primary" class="w-full" wire:click="submitQuiz">
            Submit Quiz
        </flux:button>
    </div>
@else
    <div class="space-y-6 py-8 text-center">
        @if ($quizScore >= $activeQuizPassPercentage)
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                <flux:icon.check-circle class="h-12 w-12 text-emerald-600 dark:text-emerald-400" />
            </div>
            <div>
                <h3 class="text-3xl font-bold text-zinc-950 dark:text-white">{{ $isTimestampedQuiz ? 'Checkpoint Cleared' : 'Module Complete' }}</h3>
                <p class="mt-2 text-zinc-500 dark:text-zinc-400">You scored {{ $quizScore }}%.</p>
            </div>
            <flux:button variant="primary" wire:click="{{ $isTimestampedQuiz ? 'continueTimestampedQuiz' : 'finishMainQuiz' }}">
                {{ $isTimestampedQuiz ? 'Continue Playback' : 'Continue Learning' }}
            </flux:button>
        @else
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                <flux:icon.x-circle class="h-12 w-12 text-red-600 dark:text-red-400" />
            </div>
            <div>
                <h3 class="text-3xl font-bold text-zinc-950 dark:text-white">Score: {{ $quizScore }}%</h3>
                <p class="mt-2 text-zinc-500 dark:text-zinc-400">You need {{ $activeQuizPassPercentage }}% to pass.</p>
            </div>
            <div class="mx-auto max-w-md rounded-2xl border border-amber-200 bg-amber-50 p-4 text-left dark:border-amber-900/40 dark:bg-amber-900/20">
                <div class="flex items-start gap-3">
                    <flux:icon.exclamation-triangle class="mt-0.5 h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" />
                    <p class="text-sm text-amber-800 dark:text-amber-300">
                        {{ $isTimestampedQuiz ? 'Answer the checkpoint correctly before continuing the video.' : 'Review the lesson and try again.' }}
                    </p>
                </div>
            </div>
            <div class="flex justify-center gap-3">
                @if (! $isTimestampedQuiz)
                    <flux:button variant="outline" wire:click="resetQuiz">
                        <flux:icon.arrow-path class="mr-2 h-4 w-4" />
                        Review Lesson
                    </flux:button>
                @endif
                <flux:button variant="primary" wire:click="retakeQuiz">
                    Retake Quiz
                </flux:button>
            </div>
        @endif
    </div>
@endif
