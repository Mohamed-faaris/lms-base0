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
                    <div class="mb-8 p-6 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-zinc-200 dark:border-zinc-700">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-sm font-medium">
                                    Question {{ $index + 1 }}
                                </span>
                                @if ($question['type'] === 'multiple_choice')
                                    <flux:badge color="blue" size="sm">Multiple Choice</flux:badge>
                                @else
                                    <flux:badge color="purple" size="sm">True/False</flux:badge>
                                @endif
                            </div>
                            @if (count($questions) > 1)
                                <flux:button type="button" size="xs" variant="ghost" wire:click="removeQuestion({{ $index }})" wire:confirm="Delete this question?">
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
                            <div class="mt-6">
                                <flux:label>Options</flux:label>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-4">Add at least 2 options. Select the radio button to mark the correct answer.</p>
                                
                                <div class="space-y-3">
                                    @for ($i = 0; $i < 4; $i++)
                                        @php
                                            $isCorrect = in_array(chr(65 + $i), explode(',', $question['correct_answer'] ?? ''));
                                        @endphp
                                        <div class="flex items-center gap-3 p-3 rounded-lg border transition-colors
                                            {{ $isCorrect 
                                                ? 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700' 
                                                : 'bg-white dark:bg-zinc-800 border-zinc-300 dark:border-zinc-600' }}">
                                            <div class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium
                                                {{ $isCorrect 
                                                    ? 'bg-green-500 text-white' 
                                                    : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300' }}">
                                                {{ chr(65 + $i) }}
                                            </div>
                                            <input 
                                                type="radio" 
                                                name="correct_answer_{{ $index }}"
                                                value="{{ chr(65 + $i) }}"
                                                wire:model="questions.{{ $index }}.correct_answer"
                                                class="w-4 h-4"
                                            />
                                            <input 
                                                type="text" 
                                                wire:model="questions.{{ $index }}.options.{{ $i }}"
                                                placeholder="Option {{ chr(65 + $i) }}"
                                                class="flex-1 px-4 py-2 bg-transparent border-0 focus:ring-0 focus:outline-none"
                                            />
                                            @if ($isCorrect)
                                                <flux:icon.check-circle class="w-5 h-5 text-green-600" />
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-3">
                                    Select the radio button next to the correct answer(s). You can select multiple correct answers.
                                </p>
                            </div>
                        @else
                            <flux:field class="mt-4">
                                <flux:label>Correct Answer</flux:label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer px-4 py-3 rounded-lg border transition-colors
                                        {{ ($question['correct_answer'] ?? '') === 'true' 
                                            ? 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700' 
                                            : 'bg-white dark:bg-zinc-800 border-zinc-300 dark:border-zinc-600' }}">
                                        <input 
                                            type="radio" 
                                            name="true_false_{{ $index }}"
                                            value="true"
                                            wire:model="questions.{{ $index }}.correct_answer"
                                            class="w-4 h-4"
                                        />
                                        <span class="text-sm text-zinc-700 dark:text-zinc-300">True</span>
                                        @if (($question['correct_answer'] ?? '') === 'true')
                                            <flux:icon.check-circle class="w-4 h-4 text-green-600 ml-2" />
                                        @endif
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer px-4 py-3 rounded-lg border transition-colors
                                        {{ ($question['correct_answer'] ?? '') === 'false' 
                                            ? 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700' 
                                            : 'bg-white dark:bg-zinc-800 border-zinc-300 dark:border-zinc-600' }}">
                                        <input 
                                            type="radio" 
                                            name="true_false_{{ $index }}"
                                            value="false"
                                            wire:model="questions.{{ $index }}.correct_answer"
                                            class="w-4 h-4"
                                        />
                                        <span class="text-sm text-zinc-700 dark:text-zinc-300">False</span>
                                        @if (($question['correct_answer'] ?? '') === 'false')
                                            <flux:icon.check-circle class="w-4 h-4 text-green-600 ml-2" />
                                        @endif
                                    </label>
                                </div>
                            </flux:field>
                        @endif
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