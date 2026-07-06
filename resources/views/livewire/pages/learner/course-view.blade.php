<?php

use App\Enums\ProgressStatus;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseModule;
use App\Models\LearningProgress;
use App\Models\ModuleItem;
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

    public function mount(Course $course, $item = null): void
    {
        $this->course = $course;

        $this->enrollment = CourseEnrollment::with('courseVersion')
            ->where('student_id', auth()->id())
            ->whereHas('courseVersion', fn ($q) => $q->where('course_id', $course->id))
            ->firstOrFail();

        $this->modules = CourseModule::with([
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
                            <div class="p-6">
                                <h1 class="text-xl font-semibold text-gray-900 mb-4">{{ $selectedItem->title }}</h1>

                                @if ($asset && $asset->storage->value === 'youtube' && data_get($asset, 'metadata.youtube_id'))
                                    @php
                                        $vidProgress = $this->progress->get($selectedItem->id);
                                        $lastSecond = $vidProgress && $vidProgress->videoSession
                                            ? $vidProgress->videoSession->last_second
                                            : 0;
                                    @endphp

                                    <div
                                        x-data="youtubePlayer('{{ $asset->metadata['youtube_id'] }}', {{ $lastSecond }})"
                                    >
                                        <div class="aspect-video bg-black rounded-lg overflow-hidden mb-3">
                                            <div id="yt-player"></div>
                                        </div>

                                        <div class="space-y-1 mb-4">
                                            <div class="relative h-2 bg-gray-200 rounded-full overflow-hidden">
                                                <div
                                                    class="absolute inset-y-0 left-0 bg-indigo-500 rounded-full transition-all duration-150"
                                                    :style="`width: ${progressPercent}%`"
                                                ></div>
                                                <template x-if="currentTime > 0">
                                                    <div
                                                        class="absolute top-1/2 -translate-y-1/2 w-3.5 h-3.5 bg-indigo-600 border-2 border-white rounded-full shadow-md"
                                                        :style="`left: calc(${progressPercent}% - 7px)`"
                                                    ></div>
                                                </template>
                                            </div>
                                            <div class="flex items-center justify-between text-xs text-gray-500">
                                                <span x-text="formattedTime"></span>
                                                <span x-text="formattedDuration"></span>
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
                            <div class="p-6">
                                <h1 class="text-xl font-semibold text-gray-900 mb-4">{{ $selectedItem->title }}</h1>

                                @php $quiz = $selectedItem->quiz; @endphp

                                @if ($quiz)
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

                                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                            <div class="flex items-start gap-3">
                                                <x-lucide-alert-triangle class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" />
                                                <div class="text-sm text-amber-800">
                                                    <p class="font-medium">Quiz not yet attempted</p>
                                                    <p class="mt-1">You have {{ $quiz->attempt_limit }} attempt(s) remaining. Duration: {{ $quiz->duration }} minutes. You need at least {{ $quiz->passing_marks }} points to pass.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
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
