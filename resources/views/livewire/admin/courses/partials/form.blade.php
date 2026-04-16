@php
    $previewSlug = $slug !== '' ? $slug : \Illuminate\Support\Str::slug($title);
    $previewAudience = array_values(array_filter(preg_split('/\r\n|\r|\n/', $audience) ?: []));
    $previewOutcomes = array_values(array_filter(preg_split('/\r\n|\r|\n/', $outcomes) ?: []));
    $previewRequirements = array_values(array_filter(preg_split('/\r\n|\r|\n/', $requirements) ?: []));
    $previewTags = array_values(array_filter(array_map('trim', explode(',', $tags)) ?: []));
    $existingThumbnailUrl = $course?->getFirstMediaUrl('course-thumbnail') ?: $course?->courseMeta?->thumbnail;
    $previewThumbnailUrl = $thumbnailUpload?->temporaryUrl() ?? $existingThumbnailUrl;
    $topicCount = $course?->topics->count() ?? 0;
    $moduleCount = $course?->topics->sum(fn ($topic) => $topic->modules->count()) ?? 0;
    $contentCount = $course?->topics->sum(fn ($topic) => $topic->modules->sum(fn ($module) => $module->contents->count())) ?? 0;
    $enrollmentCount = $course?->enrollments->count() ?? 0;
    $contentQuizCount = $course?->topics->sum(fn ($topic) => $topic->modules->sum(fn ($module) => $module->contents->filter(fn ($content) => $content->type?->value === 'quiz')->count())) ?? 0;
    $endQuizCount = $course?->topics->sum(fn ($topic) => $topic->modules->sum(fn ($module) => $module->contents->filter(fn ($content) => $content->endQuiz !== null)->count())) ?? 0;
    $timestampedQuizCount = $course?->topics->sum(fn ($topic) => $topic->modules->sum(fn ($module) => $module->contents->sum(fn ($content) => $content->timestampedQuizzes->count()))) ?? 0;
    $courseId = $course?->id;
@endphp

<div class="mx-auto max-w-7xl space-y-6">
    <div class="overflow-hidden rounded-[2rem] border border-zinc-200 bg-linear-to-br from-white via-white to-amber-50 shadow-sm dark:border-zinc-700 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-800">
        <div class="grid gap-6 px-6 py-6 lg:grid-cols-[auto_minmax(0,1fr)_auto] lg:px-8">
            <div class="flex items-start">
                <flux:button variant="ghost" href="{{ $backUrl }}" wire:navigate icon="arrow-left" />
            </div>

            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center rounded-full border border-amber-300 bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-amber-800 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-300">
                        {{ $isEditing ? 'Course Studio / Edit' : 'Course Studio / Create' }}
                    </span>
                    @if ($difficulty !== '')
                        <span class="inline-flex items-center rounded-full border border-zinc-300 bg-white px-3 py-1 text-xs font-medium text-zinc-700 dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-200">
                            {{ $difficulty }}
                        </span>
                    @endif
                    @if ($category !== '')
                        <span class="inline-flex items-center rounded-full border border-zinc-300 bg-white px-3 py-1 text-xs font-medium text-zinc-700 dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-200">
                            {{ $category }}
                        </span>
                    @endif
                </div>

                <div class="space-y-2">
                    <flux:heading level="1" size="xl">{{ $pageTitle }}</flux:heading>
                    <p class="max-w-3xl text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $pageSummary }}</p>
                </div>

                @if ($isEditing)
                    @include('livewire.admin.courses.partials.nav', ['course' => $course])
                @endif

                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/70 bg-white/80 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900/80">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Slug</p>
                        <p class="mt-2 font-mono text-sm text-zinc-900 dark:text-zinc-100">/{{ $previewSlug !== '' ? $previewSlug : 'your-course-path' }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/70 bg-white/80 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900/80">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Duration</p>
                        <p class="mt-2 text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $duration !== '' ? $duration : 'Add a completion estimate' }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/70 bg-white/80 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900/80">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Structure</p>
                        <p class="mt-2 text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $topicCount }} topics, {{ $moduleCount }} modules</p>
                    </div>
                </div>
            </div>

            <div class="flex items-start justify-end">
                @if ($isEditing)
                    <flux:button href="{{ route('admin.courses.show', $courseId) }}" wire:navigate variant="outline">
                        Open Course
                    </flux:button>
                @endif
            </div>
        </div>
    </div>

    <form wire:submit="save" class="grid gap-6 xl:grid-cols-[minmax(0,1.55fr)_minmax(320px,0.95fr)]">
        <div class="space-y-6">
            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <flux:heading level="2" size="lg">Course Identity</flux:heading>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Give the course a clear title, a readable path, and a concise positioning statement.</p>
                    </div>
                    <div class="rounded-2xl bg-zinc-100 px-4 py-3 text-right dark:bg-zinc-900">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Preview Label</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $title !== '' ? $title : 'Untitled course' }}</p>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <flux:field>
                            <flux:label>Title</flux:label>
                            <flux:input wire:model.live.debounce.300ms="title" placeholder="Instructional Design Essentials" required />
                            <flux:error name="title" />
                        </flux:field>
                    </div>

                    <div class="md:col-span-2">
                        <flux:field>
                            <flux:label>Slug</flux:label>
                            <flux:input wire:model.live.debounce.300ms="slug" placeholder="Auto-generated from the title" />
                            <flux:error name="slug" />
                        </flux:field>
                    </div>

                    <div class="md:col-span-2">
                        <flux:field>
                            <flux:label>Description</flux:label>
                            <flux:textarea wire:model.live.debounce.300ms="description" rows="5" placeholder="Explain what the learner will master and why the course matters." />
                            <flux:error name="description" />
                        </flux:field>
                    </div>
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                <div class="mb-6">
                    <flux:heading level="2" size="lg">Catalog Metadata</flux:heading>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Shape how this course appears in search, admin lists, and future marketing surfaces.</p>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <flux:field>
                        <flux:label>Category</flux:label>
                        <flux:input wire:model.live.debounce.300ms="category" placeholder="Teaching Practice" />
                        <flux:error name="category" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Difficulty</flux:label>
                        <flux:select wire:model.live="difficulty" placeholder="Choose difficulty">
                            <option value="">Choose difficulty</option>
                            @foreach ($difficultyOptions as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="difficulty" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Duration</flux:label>
                        <flux:input wire:model.live.debounce.300ms="duration" placeholder="4 modules • 3 hours" />
                        <flux:error name="duration" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Thumbnail Upload</flux:label>
                        <input
                            type="file"
                            wire:model="thumbnailUpload"
                            accept="image/png,image/jpeg,image/webp,image/gif"
                            class="block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-700 file:mr-3 file:rounded-lg file:border-0 file:bg-zinc-900 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-200 dark:file:bg-zinc-100 dark:file:text-zinc-900"
                        />
                        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Uploads are stored in the `course-thumbnail` media collection on the configured media disk.</p>
                        <flux:error name="thumbnailUpload" />
                    </flux:field>
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                <div class="mb-6">
                    <flux:heading level="2" size="lg">Learner Framing</flux:heading>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Use one line per item so the course can communicate who it is for and what it delivers.</p>
                </div>

                <div class="grid gap-5">
                    <flux:field>
                        <flux:label>Audience</flux:label>
                        <flux:textarea wire:model.live.debounce.300ms="audience" rows="4" placeholder="New faculty&#10;Program coordinators&#10;Assessment leads" />
                        <flux:error name="audience" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Outcomes</flux:label>
                        <flux:textarea wire:model.live.debounce.300ms="outcomes" rows="5" placeholder="Design a complete learning sequence&#10;Align activities with outcomes&#10;Measure engagement effectively" />
                        <flux:error name="outcomes" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Requirements</flux:label>
                        <flux:textarea wire:model.live.debounce.300ms="requirements" rows="3" placeholder="Basic understanding of HTML&#10;Familiarity with CSS&#10;No prior JavaScript needed" />
                        <flux:error name="requirements" />
                    </flux:field>
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                <div class="mb-6">
                    <flux:heading level="2" size="lg">Organization</flux:heading>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Help learners find this course through categorization and search.</p>
                </div>

                <div class="grid gap-5">
                    <flux:field>
                        <flux:label>Tags</flux:label>
                        <flux:input wire:model.live.debounce.300ms="tags" placeholder="javascript, react, web development, frontend" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Separate tags with commas</p>
                        <flux:error name="tags" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Instructor</flux:label>
                        <flux:input wire:model.live.debounce.300ms="instructor" placeholder="John Doe" />
                        <flux:error name="instructor" />
                    </flux:field>
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                <div class="mb-6">
                    <flux:heading level="2" size="lg">Publishing</flux:heading>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Control course visibility and prominence.</p>
                </div>

                <div class="space-y-4">
                    <label class="flex items-center justify-between rounded-xl border border-zinc-200 p-4 cursor-pointer hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-900/50">
                        <div>
                            <span class="block text-sm font-medium text-zinc-900 dark:text-zinc-100">Published</span>
                            <span class="mt-1 block text-xs text-zinc-500 dark:text-zinc-400"> learners can enroll and access course content</span>
                        </div>
                        <input type="checkbox" wire:model="published" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-zinc-200 peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-zinc-600 peer-checked:bg-blue-600"></div>
                    </label>

                    <label class="flex items-center justify-between rounded-xl border border-zinc-200 p-4 cursor-pointer hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-900/50">
                        <div>
                            <span class="block text-sm font-medium text-zinc-900 dark:text-zinc-100">Featured</span>
                            <span class="mt-1 block text-xs text-zinc-500 dark:text-zinc-400">show on homepage and course listings</span>
                        </div>
                        <input type="checkbox" wire:model="featured" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-zinc-200 peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-zinc-600 peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </section>
        </div>

        <aside class="space-y-6 xl:sticky xl:top-6 xl:self-start">
            <section class="overflow-hidden rounded-[1.75rem] border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                <div class="h-44 w-full bg-linear-to-br from-amber-200 via-orange-100 to-white dark:from-amber-900/50 dark:via-zinc-800 dark:to-zinc-900">
                    @if ($previewThumbnailUrl)
                        <img src="{{ $previewThumbnailUrl }}" alt="" class="h-full w-full object-cover" />
                    @else
                        <div class="flex h-full items-end justify-between px-6 py-5">
                            <div class="rounded-2xl bg-white/80 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-zinc-700 shadow-sm dark:bg-zinc-900/80 dark:text-zinc-200">
                                Course Preview
                            </div>
                            <div class="rounded-full border border-white/70 bg-white/70 p-3 dark:border-zinc-700 dark:bg-zinc-900/70">
                                <flux:icon.academic-cap class="h-6 w-6 text-amber-700 dark:text-amber-300" />
                            </div>
                        </div>
                    @endif
                </div>

                <div class="space-y-5 p-6">
                    <div class="space-y-3">
                        <div class="flex flex-wrap gap-2">
                            @if ($category !== '')
                                <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">{{ $category }}</span>
                            @endif
                            @if ($difficulty !== '')
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-800 dark:bg-amber-950/40 dark:text-amber-300">{{ $difficulty }}</span>
                            @endif
                            @if ($published)
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800 dark:bg-green-950/40 dark:text-green-300">Published</span>
                            @else
                                <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">Draft</span>
                            @endif
                            @if ($featured)
                                <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-800 dark:bg-purple-950/40 dark:text-purple-300">Featured</span>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $title !== '' ? $title : 'Untitled course' }}</h3>
                            <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $description !== '' ? $description : 'Add a description to see how the course will read in the admin preview.' }}</p>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Estimated Time</p>
                            <p class="mt-2 text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $duration !== '' ? $duration : 'Not set yet' }}</p>
                        </div>
                        <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Route</p>
                            <p class="mt-2 break-all font-mono text-sm text-zinc-900 dark:text-zinc-100">/{{ $previewSlug !== '' ? $previewSlug : 'course-slug' }}</p>
                        </div>
                    </div>

                    @if ($instructor !== '')
                        <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Instructor</p>
                            <p class="mt-2 text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $instructor }}</p>
                        </div>
                    @endif

                    @if (count($previewTags) > 0)
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Tags</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($previewTags as $tag)
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-950/40 dark:text-blue-300">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Audience</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @forelse ($previewAudience as $index => $item)
                                    <span wire:key="preview-audience-{{ $index }}" class="rounded-full border border-zinc-300 px-3 py-1 text-xs font-medium text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">{{ $item }}</span>
                                @empty
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">No audience cues yet.</p>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Outcomes</p>
                            <div class="mt-3 space-y-2">
                                @forelse ($previewOutcomes as $index => $item)
                                    <div wire:key="preview-outcome-{{ $index }}" class="rounded-2xl bg-zinc-100 px-4 py-3 text-sm text-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">
                                        {{ $item }}
                                    </div>
                                @empty
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">No outcomes added yet.</p>
                                @endforelse
                            </div>
                        </div>

                        @if (count($previewRequirements) > 0)
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Requirements</p>
                                <div class="mt-3 space-y-2">
                                    @foreach ($previewRequirements as $index => $item)
                                        <div wire:key="preview-requirement-{{ $index }}" class="rounded-2xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800 dark:bg-amber-950/20 dark:border-amber-800 dark:text-amber-200">
                                            {{ $item }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading level="2" size="lg">Course Snapshot</flux:heading>
                    @if ($isEditing)
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">Live course</span>
                    @endif
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Topics</p>
                        <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">{{ $topicCount }}</p>
                    </div>
                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Modules</p>
                        <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">{{ $moduleCount }}</p>
                    </div>
                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Content Items</p>
                        <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">{{ $contentCount }}</p>
                    </div>
                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Enrollments</p>
                        <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">{{ $enrollmentCount }}</p>
                    </div>
                </div>

                @if ($isEditing)
                    <p class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">
                        Last updated {{ $course?->updated_at->format('M d, Y') }}. Keep metadata and structure aligned before enrolling another cohort.
                    </p>
                @else
                    <p class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">
                        Create the shell first, then use the course page to build topics, modules, and content in order.
                    </p>
                @endif
            </section>

            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                <flux:heading level="2" size="lg">Course Structure</flux:heading>

                <div class="mt-5 space-y-3">
                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Topics</p>
                            <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">{{ $topicCount }}</span>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Modules</p>
                            <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">{{ $moduleCount }}</span>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Content Items</p>
                            <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">{{ $contentCount }}</span>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Quiz Content</p>
                            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">{{ $contentQuizCount }}</p>
                        </div>
                        <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">End Quizzes</p>
                            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">{{ $endQuizCount }}</p>
                        </div>
                        <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Timestamped Quizzes</p>
                            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">{{ $timestampedQuizCount }}</p>
                        </div>
                    </div>
                </div>

                @if ($isEditing && $courseId)
                    <div class="mt-5 flex flex-col gap-3">
                        <flux:button href="{{ route('admin.courses.show', $courseId) }}" wire:navigate variant="primary" class="w-full justify-center">
                            Manage Structure And Assessments
                        </flux:button>
                    </div>
                @elseif (!$isEditing)
                    <div class="mt-5 flex flex-col gap-3">
                        <flux:button type="submit" variant="primary" class="w-full justify-center">
                            Create Course
                        </flux:button>
                    </div>
                @endif
            </section>

            <section class="rounded-[1.75rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                <flux:heading level="2" size="lg">Actions</flux:heading>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Save when the preview feels cohesive. You can keep refining structure afterward.</p>

                <div class="mt-5 flex flex-col gap-3">
                    <flux:button type="submit" variant="primary" class="w-full justify-center">
                        {{ $submitLabel }}
                    </flux:button>
                    <flux:button href="{{ $cancelUrl }}" wire:navigate variant="outline" class="w-full justify-center">
                        Cancel
                    </flux:button>
                    @if ($isEditing && $courseId)
                        <flux:button href="{{ route('admin.courses.analyze', $courseId) }}" wire:navigate variant="ghost" class="w-full justify-center">
                            Analyze Course
                        </flux:button>
                    @endif
                </div>
            </section>
        </aside>
    </form>
</div>
