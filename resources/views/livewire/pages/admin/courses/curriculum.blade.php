<?php

use App\Enums\CourseStatus;
use App\Models\Course;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public Course $course;

    public function mount(Course $course): void
    {
        $this->course = $course;
    }

    #[Computed]
    public function version(): mixed
    {
        return $this->course->versions()->with(['modules' => fn ($q) => $q->orderBy('sort_order'), 'createdBy'])->first();
    }

    #[Computed]
    public function modules(): Collection
    {
        $version = $this->version;

        if (! $version) {
            return collect();
        }

        return $version->modules()->with(['items' => fn ($q) => $q->orderBy('sort_order')])->orderBy('sort_order')->get();
    }

    #[Computed]
    public function itemCount(): int
    {
        return $this->modules->sum(fn ($module) => $module->items->count());
    }

    #[Computed]
    public function requiredItemCount(): int
    {
        return $this->modules->sum(fn ($module) => $module->items->where('required', true)->count());
    }

    #[Computed]
    public function moduleCount(): int
    {
        return $this->modules->count();
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $course->title }}</h2>
                <p class="text-sm text-gray-500">Curriculum</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.courses.edit', $course->slug) }}" wire:navigate
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <x-dynamic-component :component="'lucide-edit-2'" class="w-4 h-4" />
                    Edit Curriculum
                </a>
                <span @class([
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    'bg-yellow-100 text-yellow-800' => $course->status === CourseStatus::DRAFT,
                    'bg-green-100 text-green-800' => $course->status === CourseStatus::PUBLISHED,
                    'bg-gray-100 text-gray-800' => $course->status === CourseStatus::ARCHIVED,
                ])>
                    {{ ucfirst($course->status->value) }}
                </span>
                <a href="{{ route('admin.courses.index') }}" wire:navigate
                   class="flex items-center gap-1.5 text-sm text-gray-600 hover:text-gray-900">
                    <x-dynamic-component :component="'lucide-arrow-left'" class="w-4 h-4" />
                    Back to Courses
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Course Overview Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 flex-wrap">
                                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $course->title }}</h3>
                                <span class="text-xs text-gray-400 font-mono whitespace-nowrap">/{{ $course->slug }}</span>
                                
                            </div>
                           
                        </div>

                        {{-- Compact Stats Row --}}
                        <dl class="flex flex-wrap items-center gap-4 sm:gap-6 text-sm w-full sm:w-auto border-t sm:border-t-0 sm:border-l sm:pl-6 pt-4 sm:pt-0">
                            <div class="flex items-center gap-2">
                                <x-dynamic-component :component="'lucide-layers'" class="w-4 h-4 text-gray-400" />
                                <span class="text-gray-500">Modules</span>
                                <span class="font-semibold text-gray-900">{{ $this->moduleCount }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-dynamic-component :component="'lucide-list'" class="w-4 h-4 text-gray-400" />
                                <span class="text-gray-500">Items</span>
                                <span class="font-semibold text-gray-900">{{ $this->itemCount }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-dynamic-component :component="'lucide-check-circle-2'" class="w-4 h-4 text-indigo-500" />
                                <span class="text-gray-500">Required</span>
                                <span class="font-semibold text-indigo-600">{{ $this->requiredItemCount }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-dynamic-component :component="'lucide-flag'" class="w-4 h-4 text-gray-400" />
                                <span class="text-gray-500">Optional</span>
                                <span class="font-semibold text-gray-500">{{ $this->itemCount - $this->requiredItemCount }}</span>
                            </div>
                            @if ($this->version)
                                <div class="flex items-center gap-2 sm:hidden">
                                    <x-dynamic-component :component="'lucide-git-branch'" class="w-4 h-4 text-gray-400" />
                                    <span class="text-gray-500">v{{ $this->version->version }}</span>
                                </div>
                            @endif
                        </dl>
                    </div>
                    @if ($course->description)
                                <p class="mt-3 text-sm text-gray-600 line-clamp-3">{{ $course->description }}</p>
                            @else
                                <p class="mt-3 text-sm text-gray-400 italic">No description provided.</p>
                            @endif
                            <span @class([
                                    'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                                    'bg-yellow-100 text-yellow-700' => $course->status === CourseStatus::DRAFT,
                                    'bg-green-100 text-green-700' => $course->status === CourseStatus::PUBLISHED,
                                    'bg-gray-100 text-gray-700' => $course->status === CourseStatus::ARCHIVED,
                                ])>
                                    {{ ucfirst($course->status->value) }}
                                </span>
                </div>
            </div>

            {{-- Version Info Bar --}}
            @if ($this->version)
                <div class="bg-gray-50 border border-gray-100 rounded-lg px-4 py-2 sm:px-6">
                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        <div class="flex items-center gap-2 text-gray-500">
                            <x-dynamic-component :component="'lucide-git-branch'" class="w-4 h-4" />
                            <span>Version <span class="font-mono font-medium text-gray-900">{{ $this->version->version }}</span></span>
                        </div>
                        @if ($this->version->published_at)
                            <div class="flex items-center gap-2 text-gray-500">
                                <x-dynamic-component :component="'lucide-calendar'" class="w-4 h-4" />
                                <span>Published <span class="font-medium text-gray-900">{{ $this->version->published_at->format('M j, Y') }}</span></span>
                                @if ($this->version->createdBy)
                                    <span class="text-gray-400">by</span>
                                    <span class="font-medium text-gray-900">{{ $this->version->createdBy->name }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Modules --}}
            @forelse ($this->modules as $module)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100" wire:key="view-module-{{ $module->id }}">
                    {{-- Module Header --}}
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 text-sm font-medium">
                                {{ $module->sort_order }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-gray-900 truncate">{{ $module->title }}</h3>
                                <p class="text-xs text-gray-500">
                                    {{ $module->items->count() }} item{{ $module->items->count() !== 1 ? 's' : '' }}
                                    ({{ $module->items->where('required', true)->count() }} required)
                                </p>
                            </div>
                        </div>
                        @if ($module->description)
                            <p class="mt-2 ml-11 text-sm text-gray-600">{{ $module->description }}</p>
                        @endif
                    </div>

                    {{-- Module Items --}}
                    <div class="divide-y divide-gray-100">
                        @forelse ($module->items as $item)
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors"
                                 wire:key="view-item-{{ $item->id }}">
                                <div class="flex items-center gap-4 min-w-0 flex-1">
                                    <span class="text-xs text-gray-400 font-medium w-6 text-right">{{ $item->sort_order }}.</span>

                                    @php
                                        $icon = match($item->type->value) {
                                            'video' => 'play-circle',
                                            'pdf' => 'file-text',
                                            'quiz' => 'clipboard-check',
                                            'assignment' => 'scroll-text',
                                            'survey' => 'clipboard-list',
                                            'external_link' => 'external-link',
                                            'custom_page' => 'file',
                                            'article' => 'file-text',
                                            default => 'file',
                                        };
                                        $iconColors = match($item->type->value) {
                                            'video' => 'text-red-500 bg-red-50',
                                            'pdf' => 'text-red-500 bg-red-50',
                                            'quiz' => 'text-purple-500 bg-purple-50',
                                            'assignment' => 'text-orange-500 bg-orange-50',
                                            'survey' => 'text-teal-500 bg-teal-50',
                                            'external_link' => 'text-blue-500 bg-blue-50',
                                            'article' => 'text-green-500 bg-green-50',
                                            'custom_page' => 'text-indigo-500 bg-indigo-50',
                                            default => 'text-gray-400 bg-gray-100',
                                        };
                                    @endphp
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ $iconColors }}">
                                        <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4" />
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $item->title }}</p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-gray-600 bg-gray-100">
                                                {{ ucfirst($item->type->value) }}
                                            </span>
                                            @if (!$item->required)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-gray-500 bg-gray-50">
                                                    <x-dynamic-component :component="'lucide-flag'" class="w-3 h-3 mr-1" />
                                                    Optional
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-indigo-600 bg-indigo-50">
                                                    <x-dynamic-component :component="'lucide-check-circle-2'" class="w-3 h-3 mr-1" />
                                                    Required
                                                </span>
                                            @endif
                                            @if ($item->settings && $item->settings !== '[]')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-gray-500 bg-gray-50"
                                                      title="Has configuration">
                                                    <x-dynamic-component :component="'lucide-settings'" class="w-3 h-3 mr-1" />
                                                    Configured
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Item Settings Preview --}}
                                @if ($item->settings && $item->settings !== '[]')
                                    <div class="ml-4 flex items-center">
                                        <button type="button"
                                                class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                                                wire:click="$dispatch('view-item-settings', { settings: @json($item->settings), title: '{{ addslashes($item->title) }}' })"
                                                title="View settings">
                                            <x-dynamic-component :component="'lucide-eye'" class="w-4 h-4" />
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center">
                                <x-dynamic-component :component="'lucide-folder-open'" class="w-8 h-8 text-gray-300 mx-auto mb-2" />
                                <p class="text-sm text-gray-500">No items in this module yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 mb-4">
                        <x-dynamic-component :component="'lucide-book-open'" class="w-8 h-8 text-indigo-500" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No curriculum yet</h3>
                    <p class="text-sm text-gray-500 mb-6 max-w-xs mx-auto">
                        Start building your course by adding modules and learning items.
                    </p>
                    <a href="{{ route('admin.courses.edit', $course->slug) }}" wire:navigate
                       class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-500 transition-colors">
                        <x-dynamic-component :component="'lucide-plus'" class="w-4 h-4" />
                        Build Curriculum
                    </a>
                </div>
            @endforelse

        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Livewire.on('view-item-settings', (event) => {
                const { settings, title } = event.detail;
                alert(`Settings for "${title}":\n\n${JSON.stringify(settings, null, 2)}`);
            });
        });
    </script>
@endpush