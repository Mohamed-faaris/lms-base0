<div>
    <div class="flex items-center gap-4 mb-6">
        <flux:button variant="ghost" href="{{ route('admin.courses.show', $course?->id) }}" wire:navigate icon="arrow-left" />
        <div class="flex-1">
            <flux:heading level="1" size="xl">{{ $moduleQuiz ? 'Edit' : 'Create' }} Module Quiz</flux:heading>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                {{ $course?->title ?? 'Select a course' }}
            </p>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
        <form wire:submit="save" class="space-y-6">
            <flux:field>
                <flux:label>Module</flux:label>
                <flux:select wire:model="moduleId" required>
                    <flux:select.option value="">Select a module</flux:select.option>
                    @foreach ($availableModules as $module)
                        <flux:select.option value="{{ $module['id'] }}">
                            {{ $module['title'] }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="moduleId" />
            </flux:field>

            <div class="border-t border-zinc-200 dark:border-zinc-700 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <flux:heading level="2" size="lg">Questions</flux:heading>
                    <flux:button type="button" size="sm" wire:click="addQuestion()">
                        <flux:icon.plus variant="mini" />
                        Add Question
                    </flux:button>
                </div>

                @foreach ($questions as $index => $question)
                    <div class="mb-6 p-4 bg-zinc-50 dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Question {{ $index + 1 }}</span>
                            @if (count($questions) > 1)
                                <flux:button type="button" size="xs" variant="ghost" wire:click="removeQuestion({{ $index }})">
                                    <flux:icon.trash class="w-4 h-4 text-red-500" />
                                </flux:button>
                            @endif
                        </div>

                        <flux:field>
                            <flux:label>Question Text</flux:label>
                            <flux:textarea wire:model="questions.{{ $index }}.question_text" placeholder="Enter your question" required />
                        </flux:field>

                        <flux:field class="mt-4">
                            <flux:label>Question Type</flux:label>
                            <flux:select wire:model="questions.{{ $index }}.type" required>
                                <flux:select.option value="multiple_choice">Multiple Choice</flux:select.option>
                                <flux:select.option value="true_false">True/False</flux:select.option>
                            </flux:select>
                        </flux:field>

                        @if ($question['type'] === 'multiple_choice')
                            <flux:field class="mt-4">
                                <flux:label>Options (one per line)</flux:label>
                                <flux:textarea wire:model="questions.{{ $index }}.options_text" placeholder="Option A&#10;Option B&#10;Option C&#10;Option D" />
                            </flux:field>
                        @endif

                        <flux:field class="mt-4">
                            <flux:label>Correct Answer</flux:label>
                            @if ($question['type'] === 'multiple_choice')
                                <flux:input wire:model="questions.{{ $index }}.correct_answer" placeholder="A or B or C or D" />
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Separate multiple correct answers with commas (e.g., "A,C")</p>
                            @else
                                <flux:select wire:model="questions.{{ $index }}.correct_answer" required>
                                    <flux:select.option value="true">True</flux:select.option>
                                    <flux:select.option value="false">False</flux:select.option>
                                </flux:select>
                            @endif
                        </flux:field>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="outline" href="{{ route('admin.courses.show', $course?->id) }}" wire:navigate>
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $moduleQuiz ? 'Update' : 'Create' }} Module Quiz
                </flux:button>
            </div>
        </form>
    </div>
</div>
