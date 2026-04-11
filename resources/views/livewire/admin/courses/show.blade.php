@php
    $thumbnailUrl = $course->getFirstMediaUrl('course-thumbnail') ?: $course->courseMeta?->thumbnail;
@endphp

<div class="space-y-6">
    <div class="overflow-hidden rounded-[2rem] border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
        <div class="grid gap-6 px-6 py-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:px-8">
            <div class="flex items-start gap-4">
                <flux:button variant="ghost" href="{{ route('admin.courses.index') }}" wire:navigate icon="arrow-left" />
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-blue-800 dark:bg-blue-950/40 dark:text-blue-300">
                            Course Viewer
                        </span>
                        @if ($course->courseMeta?->category)
                            <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">
                                {{ $course->courseMeta->category }}
                            </span>
                        @endif
                        @if ($course->courseMeta?->difficulty)
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-800 dark:bg-amber-950/40 dark:text-amber-300">
                                {{ $course->courseMeta->difficulty }}
                            </span>
                        @endif
                    </div>

                    <div>
                        <flux:heading level="1" size="xl">{{ $course->title }}</flux:heading>
                        <p class="mt-1 font-mono text-sm text-zinc-500 dark:text-zinc-400">/{{ $course->slug }}</p>
                        <p class="mt-3 max-w-3xl text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                            {{ $course->description ?: 'No course description yet.' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-start justify-end">
                @include('livewire.admin.courses.partials.nav', ['course' => $course])
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

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

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.55fr)_minmax(320px,0.95fr)]">
        <div class="space-y-6">
            @forelse ($course->topics as $topic)
                <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800" wire:key="show-topic-{{ $topic->id }}">
                    <div class="flex flex-col gap-4 border-b border-zinc-200 pb-5 dark:border-zinc-700 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">Topic</span>
                                <flux:badge color="zinc" size="sm">{{ $topic->modules->count() }} modules</flux:badge>
                            </div>
                            @if ($quickEditType === 'topic' && $quickEditId === $topic->id)
                                <div class="mt-4 space-y-3">
                                    <flux:input wire:model="quickEditTitle" />
                                    <flux:textarea wire:model="quickEditDescription" rows="3" />
                                    <div class="flex gap-2">
                                        <flux:button size="sm" wire:click="saveQuickEdit">Save</flux:button>
                                        <flux:button size="sm" variant="outline" wire:click="cancelQuickEdit">Cancel</flux:button>
                                    </div>
                                </div>
                            @else
                                <h2 class="mt-3 text-xl font-semibold text-zinc-950 dark:text-white">{{ $topic->name }}</h2>
                                <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $topic->description ?: 'No topic description yet.' }}</p>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <flux:tooltip content="Quick edit topic" position="top">
                                <flux:button size="sm" variant="ghost" wire:click="startQuickEdit('topic', {{ $topic->id }})" icon="pencil-square" />
                            </flux:tooltip>
                            <flux:tooltip content="Open structure" position="top">
                                <flux:button size="sm" variant="outline" href="{{ route('admin.courses.structure', $course->id) }}" wire:navigate icon="squares-2x2" />
                            </flux:tooltip>
                        </div>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse ($topic->modules as $module)
                            <div class="rounded-2xl border border-zinc-200 dark:border-zinc-700" wire:key="show-module-{{ $module->id }}">
                                <div class="flex flex-col gap-4 bg-zinc-50 px-5 py-4 dark:bg-zinc-900/50 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">Module</span>
                                            <flux:badge color="zinc" size="xs">{{ $module->contents->count() }} content</flux:badge>
                                            <flux:badge color="purple" size="xs">{{ $module->contents->where('type', \App\Enums\ContentType::Quiz)->count() }} quiz content</flux:badge>
                                        </div>

                                        @if ($quickEditType === 'module' && $quickEditId === $module->id)
                                            <div class="mt-4 space-y-3">
                                                <flux:input wire:model="quickEditTitle" />
                                                <flux:textarea wire:model="quickEditDescription" rows="3" />
                                                <div class="flex gap-2">
                                                    <flux:button size="sm" wire:click="saveQuickEdit">Save</flux:button>
                                                    <flux:button size="sm" variant="outline" wire:click="cancelQuickEdit">Cancel</flux:button>
                                                </div>
                                            </div>
                                        @else
                                            <h3 class="mt-3 text-lg font-semibold text-zinc-950 dark:text-white">{{ $module->title }}</h3>
                                            <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $module->description ?: 'No module description yet.' }}</p>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <flux:tooltip content="Quick edit module" position="top">
                                            <flux:button size="sm" variant="ghost" wire:click="startQuickEdit('module', {{ $module->id }})" icon="pencil-square" />
                                        </flux:tooltip>
                                        <flux:button size="sm" variant="ghost" href="{{ route('admin.courses.structure', $course->id) }}" wire:navigate>Open Structure</flux:button>
                                    </div>
                                </div>

                                <div class="space-y-3 p-5">
                                    @forelse ($module->contents as $content)
                                        <article class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800/80" wire:key="show-content-{{ $content->id }}">
                                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                                <div class="flex-1">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <flux:badge color="zinc" size="xs">{{ strtoupper($content->type->value) }}</flux:badge>
                                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Order {{ $content->order }}</span>
                                                        @if ($content->type->value === 'video')
                                                            <flux:badge color="blue" size="xs">{{ $content->timestampedQuizzes->count() }} timestamps</flux:badge>
                                                        @endif
                                                        @if ($content->type->value === 'quiz')
                                                            <flux:badge color="emerald" size="xs">{{ $content->quiz?->questions?->count() ?? 0 }} quiz questions</flux:badge>
                                                        @endif
                                                        @if ($content->endQuiz)
                                                            <flux:badge color="green" size="xs">End quiz</flux:badge>
                                                        @endif
                                                    </div>

                                                    @if ($quickEditType === 'content' && $quickEditId === $content->id)
                                                        <div class="mt-4 space-y-3">
                                                            <flux:input wire:model="quickEditTitle" />
                                                            <flux:textarea wire:model="quickEditDescription" rows="4" />
                                                            <flux:input wire:model="quickEditUrl" placeholder="Content URL" />
                                                            <div class="flex gap-2">
                                                                <flux:button size="sm" wire:click="saveQuickEdit">Save</flux:button>
                                                                <flux:button size="sm" variant="outline" wire:click="cancelQuickEdit">Cancel</flux:button>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <h4 class="mt-3 text-base font-semibold text-zinc-950 dark:text-white">{{ $content->title }}</h4>
                                                        <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $content->body ? \Illuminate\Support\Str::limit($content->body, 220) : 'No body text yet.' }}</p>
                                                        @if ($content->content_url)
                                                            <a href="{{ $content->content_url }}" target="_blank" class="mt-3 inline-block text-sm text-blue-600 hover:underline dark:text-blue-400">
                                                                {{ $content->content_url }}
                                                            </a>
                                                        @endif
                                                    @endif

                                                    <div class="mt-4 flex flex-wrap gap-2">
                                                        @if ($content->type->value === 'video')
                                                            <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs text-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">
                                                                {{ $content->timestampedQuizzes->count() }} timestamp quizzes
                                                            </span>
                                                            @if ($content->endQuiz)
                                                                <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs text-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">
                                                                    End quiz: {{ $content->endQuiz->questions->count() }} questions
                                                                </span>
                                                            @endif
                                                        @endif
                                                        @if ($content->type->value === 'quiz')
                                                            <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs text-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">
                                                                Main quiz: {{ $content->quiz?->questions?->count() ?? 0 }} questions
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="flex flex-wrap gap-2">
                                                    <flux:tooltip content="Quick edit content" position="top">
                                                        <flux:button size="sm" variant="ghost" wire:click="startQuickEdit('content', {{ $content->id }})" icon="pencil-square" />
                                                    </flux:tooltip>
                                                    <flux:tooltip content="Open full editor" position="top">
                                                        <flux:button size="sm" variant="ghost" href="{{ route('admin.courses.content.edit', [$course->id, $content->id]) }}" wire:navigate icon="arrow-top-right-on-square" />
                                                    </flux:tooltip>
                                                    @if ($content->type->value === 'quiz')
                                                        <flux:button size="sm" variant="ghost" href="{{ route('admin.courses.content.quiz.edit', [$course->id, $content->id]) }}" wire:navigate>Quiz Editor</flux:button>
                                                    @elseif ($content->endQuiz)
                                                        <flux:button size="sm" variant="ghost" href="{{ route('admin.courses.content.end-quiz.edit', [$course->id, $content->id]) }}" wire:navigate>End Quiz</flux:button>
                                                    @endif
                                                </div>
                                            </div>
                                        </article>
                                    @empty
                                        <div class="rounded-xl border border-dashed border-zinc-300 px-4 py-6 text-sm text-zinc-500 dark:border-zinc-600 dark:text-zinc-400">
                                            No content yet.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-zinc-300 px-4 py-6 text-sm text-zinc-500 dark:border-zinc-600 dark:text-zinc-400">
                                No modules yet.
                            </div>
                        @endforelse
                    </div>
                </section>
            @empty
                <div class="rounded-[1.75rem] border border-dashed border-zinc-300 bg-white px-6 py-12 text-center text-sm text-zinc-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                    No topics or modules yet. Use the Structure page to start building the course.
                </div>
            @endforelse
        </div>

        <aside class="space-y-6">
            <section class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                <div class="h-44 w-full bg-linear-to-br from-amber-200 via-orange-100 to-white dark:from-amber-900/50 dark:via-zinc-800 dark:to-zinc-900">
                    @if ($thumbnailUrl)
                        <img src="{{ $thumbnailUrl }}" alt="" class="h-full w-full object-cover" />
                    @endif
                </div>
                <div class="space-y-4 p-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Duration</p>
                        <p class="mt-2 text-sm font-semibold text-zinc-950 dark:text-white">{{ $course->courseMeta?->duration ?: 'Not set yet' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Audience</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @forelse ($course->courseMeta?->data['audience'] ?? [] as $audience)
                                <span class="rounded-full border border-zinc-300 px-3 py-1 text-xs font-medium text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">{{ $audience }}</span>
                            @empty
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">No audience framing yet.</p>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Outcomes</p>
                        <div class="mt-3 space-y-2">
                            @forelse ($course->courseMeta?->data['outcomes'] ?? [] as $outcome)
                                <div class="rounded-2xl bg-zinc-100 px-4 py-3 text-sm text-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">{{ $outcome }}</div>
                            @empty
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">No outcomes listed yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                <flux:heading level="2" size="lg">Editing Guidance</flux:heading>
                <div class="mt-4 space-y-3 text-sm text-zinc-600 dark:text-zinc-300">
                    <p>Use this page to read through the whole course and make lightweight text changes without leaving the viewer.</p>
                    <p>Use <a href="{{ route('admin.courses.structure', $course->id) }}" wire:navigate class="font-medium text-blue-600 hover:underline dark:text-blue-400">Structure</a> for adding or reorganizing topics, modules, content, and assessments.</p>
                    <p>Use <a href="{{ route('admin.courses.edit', $course->id) }}" wire:navigate class="font-medium text-blue-600 hover:underline dark:text-blue-400">Edit</a> for metadata, thumbnail, and learner-facing course positioning.</p>
                </div>
            </section>
        </aside>
    </div>
</div>
