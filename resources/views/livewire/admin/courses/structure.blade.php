<div class="space-y-6">
    <div class="flex flex-col gap-4 rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 lg:flex-row lg:items-start lg:justify-between">
        <div class="flex items-start gap-4">
            <flux:button variant="ghost" href="{{ route('admin.courses.show', $course->id) }}" wire:navigate icon="arrow-left" />
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-800 dark:bg-amber-950/40 dark:text-amber-300">
                        Course Structure
                    </span>
                    <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">
                        {{ $course->topics->count() }} topics
                    </span>
                    <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">
                        {{ $totalContent }} content items
                    </span>
                </div>
                <div>
                    <flux:heading level="1" size="xl">{{ $course->title }}</flux:heading>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Build and maintain topics, modules, content, and assessments from one workspace.</p>
                </div>
            </div>
        </div>

        @include('livewire.admin.courses.partials.nav', ['course' => $course])
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Enrollments</p>
            <p class="mt-2 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $totalEnrollments }}</p>
        </div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Completed</p>
            <p class="mt-2 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $completedEnrollments }}</p>
        </div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Avg Progress</p>
            <p class="mt-2 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $avgProgress }}%</p>
        </div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Content Items</p>
            <p class="mt-2 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $totalContent }}</p>
        </div>
    </div>

    <div class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
        <div class="mb-4 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <flux:heading level="2" size="lg">Authoring Workspace</flux:heading>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Everything is grouped by topic and module so authors can add content and assessments without digging through the viewer page.</p>
            </div>
            <flux:button size="sm" wire:click="openTopicModal()" icon="plus">Add Topic</flux:button>
        </div>

        <div class="space-y-4">
            @forelse ($course->topics as $topic)
                <section class="rounded-2xl border border-zinc-200 dark:border-zinc-700" wire:key="structure-topic-{{ $topic->id }}">
                    <div class="flex items-center gap-3 border-b border-zinc-200 bg-zinc-50 px-4 py-4 dark:border-zinc-700 dark:bg-zinc-900">
                        <button wire:click="toggleTopic({{ $topic->id }})" class="flex flex-1 items-center justify-between text-left">
                            <div class="flex items-center gap-3">
                                <flux:icon.chevron-down class="h-5 w-5 {{ !($expandedTopics[$topic->id] ?? true) ? '-rotate-90' : '' }} transition-transform" />
                                <div>
                                    <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $topic->name }}</p>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $topic->description ?: 'No topic description yet.' }}</p>
                                </div>
                            </div>
                            <flux:badge color="zinc" size="sm">{{ $topic->modules->count() }} modules</flux:badge>
                        </button>
                        <flux:tooltip content="Edit topic" position="top">
                            <flux:button size="sm" variant="ghost" wire:click="openTopicModal({{ $topic->id }})" icon="pencil-square" />
                        </flux:tooltip>
                        <flux:tooltip content="Add module" position="top">
                            <flux:button size="sm" variant="ghost" wire:click="openModuleModal(null); $set('selectedTopicId', {{ $topic->id }})" icon="plus" />
                        </flux:tooltip>
                    </div>

                    @if ($expandedTopics[$topic->id] ?? true)
                        <div class="space-y-3 p-4">
                            @forelse ($topic->modules as $module)
                                <div class="overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-700" wire:key="structure-module-{{ $module->id }}">
                                    <div class="flex items-center gap-3 bg-white px-4 py-3 dark:bg-zinc-800">
                                        <button wire:click="toggleModule({{ $module->id }})" class="flex flex-1 items-center justify-between text-left">
                                            <div class="flex items-center gap-3">
                                                <flux:icon.chevron-down class="h-4 w-4 {{ !($expandedModules[$module->id] ?? true) ? '-rotate-90' : '' }} transition-transform" />
                                                <div>
                                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $module->title }}</p>
                                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $module->description ?: 'No module description yet.' }}</p>
                                                </div>
                                            </div>
                                            <flux:badge color="zinc" size="xs">{{ $module->contents->count() }} content</flux:badge>
                                        </button>
                                        <flux:tooltip content="Edit module" position="top">
                                            <flux:button size="sm" variant="ghost" wire:click="openModuleModal({{ $module->id }})" icon="pencil-square" />
                                        </flux:tooltip>
                                        <flux:tooltip content="Add content" position="top">
                                            <flux:button size="sm" variant="ghost" wire:click="openContentModal(null); $set('selectedModuleId', {{ $module->id }})" icon="plus" />
                                        </flux:tooltip>
                                    </div>

                                    @if ($expandedModules[$module->id] ?? true)
                                        <div class="border-t border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900/40">
                                            <div class="space-y-3">
                                                @forelse ($module->contents as $content)
                                                    <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800" wire:key="structure-content-{{ $content->id }}">
                                                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                                            <div class="space-y-2">
                                                                <div class="flex flex-wrap items-center gap-2">
                                                                    <flux:badge color="zinc" size="xs">{{ strtoupper($content->type->value) }}</flux:badge>
                                                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">Order {{ $content->order }}</span>
                                                                </div>
                                                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $content->title }}</p>
                                                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $content->body ? \Illuminate\Support\Str::limit($content->body, 120) : 'No body text yet.' }}</p>
                                                                @if ($content->content_url)
                                                                    <a href="{{ $content->content_url }}" target="_blank" class="text-xs text-blue-600 hover:underline dark:text-blue-400">{{ $content->content_url }}</a>
                                                                @endif
                                                            </div>
                                                            <div class="flex flex-wrap gap-2">
                                                                <flux:tooltip content="Open full editor" position="top">
                                                                    <flux:button size="sm" variant="ghost" href="{{ route('admin.courses.content.edit', [$course->id, $content->id]) }}" wire:navigate icon="arrow-top-right-on-square" />
                                                                </flux:tooltip>
                                                                @if ($content->type->value === 'quiz')
                                                                    <flux:tooltip content="Edit quiz content" position="top">
                                                                        <flux:button size="sm" variant="ghost" href="{{ route('admin.courses.content.quiz.edit', [$course->id, $content->id]) }}" wire:navigate icon="clipboard-document-list" />
                                                                    </flux:tooltip>
                                                                @elseif ($content->endQuiz)
                                                                    <flux:tooltip content="Edit end quiz" position="top">
                                                                        <flux:button size="sm" variant="ghost" href="{{ route('admin.courses.content.end-quiz.edit', [$course->id, $content->id]) }}" wire:navigate icon="clipboard-document-check" />
                                                                    </flux:tooltip>
                                                                @else
                                                                    <flux:button size="sm" variant="ghost" href="{{ route('admin.courses.content.end-quiz.edit', [$course->id, $content->id]) }}" wire:navigate>Add End Quiz</flux:button>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="mt-4 flex flex-wrap items-center gap-2">
                                                            @if ($content->type->value === 'quiz')
                                                                <span class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Quiz Questions</span>
                                                                <flux:badge color="emerald" size="xs">{{ $content->quiz?->questions?->count() ?? 0 }}</flux:badge>
                                                            @endif

                                                            @if ($content->type->value !== 'quiz')
                                                                <span class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">End Quiz</span>
                                                                <flux:badge color="green" size="xs">{{ $content->endQuiz?->questions?->count() ?? 0 }}</flux:badge>
                                                            @endif

                                                            @if ($content->type->value === 'video')
                                                                <span class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Timestamped Quizzes</span>
                                                                <flux:badge color="blue" size="xs">{{ $content->timestampedQuizzes->count() }}</flux:badge>
                                                                <flux:button size="xs" variant="ghost" href="{{ route('admin.courses.content.timestamped-quiz.create', [$course->id, $content->id]) }}" wire:navigate>Add Timestamp</flux:button>
                                                            @endif
                                                        </div>

                                                        @if ($content->type->value === 'video' && $content->timestampedQuizzes->isNotEmpty())
                                                            <div class="mt-4 space-y-2">
                                                                @foreach ($content->timestampedQuizzes as $timestampedQuiz)
                                                                    <div class="flex items-center justify-between rounded-xl bg-zinc-50 px-3 py-2 dark:bg-zinc-900" wire:key="timestamped-quiz-{{ $timestampedQuiz->id }}">
                                                                        <div class="flex items-center gap-3">
                                                                            <flux:badge color="blue" size="xs">{{ gmdate('H:i:s', $timestampedQuiz->timestamp_seconds ?? 0) }}</flux:badge>
                                                                            <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $timestampedQuiz->questions->count() }} questions</span>
                                                                        </div>
                                                                        <flux:button size="xs" variant="ghost" href="{{ route('admin.courses.content.timestamped-quiz.edit', [$course->id, $content->id, $timestampedQuiz->id]) }}" wire:navigate>
                                                                            Edit
                                                                        </flux:button>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @empty
                                                    <div class="rounded-xl border border-dashed border-zinc-300 px-4 py-6 text-sm text-zinc-500 dark:border-zinc-600 dark:text-zinc-400">
                                                        No content items yet.
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="rounded-xl border border-dashed border-zinc-300 px-4 py-6 text-sm text-zinc-500 dark:border-zinc-600 dark:text-zinc-400">
                                    No modules yet.
                                </div>
                            @endforelse
                        </div>
                    @endif
                </section>
            @empty
                <div class="rounded-2xl border border-dashed border-zinc-300 px-6 py-10 text-center text-sm text-zinc-500 dark:border-zinc-600 dark:text-zinc-400">
                    No topics or modules yet.
                </div>
            @endforelse
        </div>
    </div>

    @include('livewire.admin.courses.partials.structure-modals')
</div>
