<div class="space-y-6">
    <div class="flex items-center gap-4 rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
        <flux:button variant="ghost" href="{{ route('admin.courses.content.show', $content->id) }}" wire:navigate icon="arrow-left" />
        <div class="flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                    {{ $this->placementKind()->label() }}
                </span>
                <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">
                    {{ $content->title }}
                </span>
            </div>
            <flux:heading class="mt-3" level="1" size="xl">{{ $quiz ? 'Edit Quiz' : 'Create Quiz' }}</flux:heading>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                {{ $content->module?->title }} · {{ $course->title }}
            </p>
        </div>
    </div>

    <form wire:submit="save" class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_360px]">
        <div class="space-y-6">
            @foreach ($questions as $index => $question)
                <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800" wire:key="quiz-question-{{ $index }}">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">
                                Question {{ $index + 1 }}
                            </span>
                        </div>
                        <flux:button type="button" size="sm" variant="ghost" wire:click="removeQuestion({{ $index }})" icon="trash">
                            Remove
                        </flux:button>
                    </div>

                    <div class="space-y-5">
                        <flux:field>
                            <flux:label>Question Text</flux:label>
                            <flux:textarea wire:model="questions.{{ $index }}.question_text" rows="4" placeholder="Enter the question prompt" />
                            <flux:error name="questions.{{ $index }}.question_text" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Question Type</flux:label>
                            <flux:select wire:model.live="questions.{{ $index }}.type">
                                <flux:select.option value="multiple_choice">Multiple Choice</flux:select.option>
                                <flux:select.option value="true_false">True / False</flux:select.option>
                            </flux:select>
                            <flux:error name="questions.{{ $index }}.type" />
                        </flux:field>

                        @if (($questions[$index]['type'] ?? 'multiple_choice') === 'multiple_choice')
                            <div class="space-y-4">
                                <div class="flex items-center justify-between gap-3">
                                    <flux:label>Options</flux:label>
                                    <flux:button type="button" size="sm" variant="ghost" wire:click="addOption({{ $index }})" icon="plus">
                                        Add Option
                                    </flux:button>
                                </div>

                                @foreach ($questions[$index]['options'] ?? [] as $optionIndex => $option)
                                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700" wire:key="question-{{ $index }}-option-{{ $optionIndex }}">
                                        <div class="flex items-start gap-3">
                                            <label class="mt-3 flex h-5 w-5 items-center justify-center">
                                                <input
                                                    type="radio"
                                                    wire:model.live="questions.{{ $index }}.correct_answer"
                                                    value="{{ $optionIndex }}"
                                                    class="h-4 w-4 border-zinc-300 text-zinc-900 focus:ring-zinc-400 dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-100"
                                                />
                                            </label>

                                            <div class="flex-1 space-y-2">
                                                <div class="flex items-center justify-between gap-3">
                                                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Option {{ chr(65 + $optionIndex) }}</p>
                                                    @if (count($questions[$index]['options'] ?? []) > 2)
                                                        <flux:button type="button" size="xs" variant="ghost" wire:click="removeOption({{ $index }}, {{ $optionIndex }})" icon="trash" />
                                                    @endif
                                                </div>
                                                <flux:input wire:model="questions.{{ $index }}.options.{{ $optionIndex }}" placeholder="Enter option {{ chr(65 + $optionIndex) }}" />
                                                <flux:error name="questions.{{ $index }}.options.{{ $optionIndex }}" />
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <flux:error name="questions.{{ $index }}.options" />
                                <flux:error name="questions.{{ $index }}.correct_answer" />
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Choose the correct option using the radio button on the left.</p>
                            </div>
                        @else
                            <flux:field>
                                <flux:label>Correct Answer</flux:label>
                                <flux:select wire:model="questions.{{ $index }}.correct_answer">
                                    <flux:select.option value="true">True</flux:select.option>
                                    <flux:select.option value="false">False</flux:select.option>
                                </flux:select>
                                <flux:error name="questions.{{ $index }}.correct_answer" />
                            </flux:field>
                        @endif
                    </div>
                </section>
            @endforeach

            <flux:button type="button" variant="outline" wire:click="addQuestion" icon="plus">
                Add Question
            </flux:button>
        </div>

        <aside class="space-y-6 xl:sticky xl:top-6 xl:self-start">
            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                <flux:heading level="2" size="lg">Placement</flux:heading>
                <div class="mt-4 space-y-3 text-sm text-zinc-600 dark:text-zinc-300">
                    <p>{{ $this->placementKind()->label() }} for <span class="font-semibold text-zinc-950 dark:text-white">{{ $content->title }}</span>.</p>
                    @if ($this->placementKind() === \App\Enums\QuizKind::Content)
                        <p>This is the main assessment for a `quiz` content item.</p>
                    @elseif ($this->placementKind() === \App\Enums\QuizKind::End)
                        <p>This assessment appears after the content item is completed.</p>
                    @else
                        <p>This assessment appears at a specific timestamp inside a video lesson.</p>
                    @endif
                </div>

                @if ($this->placementKind() === \App\Enums\QuizKind::Timestamped)
                    <div class="mt-5">
                        <flux:field>
                            <flux:label>Timestamp</flux:label>
                            <flux:input wire:model="timestamp" placeholder="00:05:30" />
                            <flux:error name="timestamp" />
                        </flux:field>
                    </div>
                @endif

                <div class="mt-5">
                    <flux:field>
                        <flux:label>Passing Score Percentage</flux:label>
                        <flux:input type="number" min="0" max="100" wire:model="scorePercentage" placeholder="0" />
                        <flux:error name="scorePercentage" />
                        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Leave empty to allow continuation at 0%.</p>
                    </flux:field>
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                <flux:heading level="2" size="lg">Actions</flux:heading>
                <div class="mt-5 flex flex-col gap-3">
                    <flux:button type="submit" variant="primary" class="w-full justify-center">
                        Save Quiz
                    </flux:button>
                    <flux:button href="{{ route('admin.courses.content.show', $content->id) }}" wire:navigate variant="outline" class="w-full justify-center">
                        Cancel
                    </flux:button>
                </div>
            </section>
        </aside>
    </form>
</div>
