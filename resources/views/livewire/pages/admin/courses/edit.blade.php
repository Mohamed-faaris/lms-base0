<?php

use App\Enums\CourseStatus;
use App\Enums\ModuleItemType;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\ModuleItem;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {
    public Course $course;

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

    public bool $showModuleForm = false;
    public bool $showItemForm = false;
    public ?CourseModule $editingModule = null;
    public ?ModuleItem $editingItem = null;

    public string $moduleTitle = '';
    public string $moduleDescription = '';

    public string $itemTitle = '';
    public string $itemType = 'video';
    public bool $itemRequired = true;
    public string $itemSettings = '';

    public bool $showCourseForm = false;
    public string $courseTitle = '';
    public string $courseSlug = '';
    public string $courseDescription = '';
    public string $courseStatus = 'draft';

    public int $moduleSort = 0;
    public int $itemSort = 0;

    protected function rules(): array
    {
        return [
            'moduleTitle' => ['required', 'string', 'max:255'],
            'moduleDescription' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function mount(Course $course): void
    {
        $this->course = $course->load('versions.modules.items');
        $this->courseTitle = $course->title;
        $this->courseSlug = $course->slug;
        $this->courseDescription = $course->description ?? '';
        $this->courseStatus = $course->status->value;
    }
    

    public function saveCourse(): void
    {
        $this->validate([
            'courseTitle' => ['required', 'string', 'max:255'],
            'courseSlug' => ['required', 'string', 'max:255', 'unique:courses,slug,' . $this->course->id],
            'courseDescription' => ['nullable', 'string', 'max:5000'],
            'courseStatus' => ['required', 'in:draft,published,archived'],
        ]);

        $this->course->update([
            'title' => $this->courseTitle,
            'slug' => $this->courseSlug,
            'description' => $this->courseDescription ?: null,
            'status' => CourseStatus::from($this->courseStatus),
        ]);

        $this->showCourseForm = false;
    }
public function test()
{
    logger('clicked');
    $this->showCourseForm = ! $this->showCourseForm;
}
    public function startAddModule(): void
    {
        $this->showModuleForm = true;
        $this->showItemForm = false;
        $this->editingModule = null;
        $this->editingItem = null;
        $this->moduleTitle = '';
        $this->moduleDescription = '';
    }

    public function startEditModule(int $moduleId): void
    {
        $module = CourseModule::findOrFail($moduleId);
        $this->showModuleForm = true;
        $this->showItemForm = false;
        $this->editingModule = $module;
        $this->editingItem = null;
        $this->moduleTitle = $module->title;
        $this->moduleDescription = $module->description ?? '';
    }

    public function saveModule(): void
    {
        $this->validate();

        $version = $this->course->versions()->first();

        if ($this->editingModule) {
            $this->editingModule->update([
                'title' => $this->moduleTitle,
                'description' => $this->moduleDescription ?: null,
            ]);
        } else {
            $maxSort = CourseModule::where('course_version_id', $version->id)->max('sort_order') ?? 0;

            CourseModule::create([
                'course_version_id' => $version->id,
                'title' => $this->moduleTitle,
                'description' => $this->moduleDescription ?: null,
                'sort_order' => $maxSort + 1,
            ]);
        }

        $this->showModuleForm = false;
        $this->moduleTitle = '';
        $this->moduleDescription = '';
    }

    public function deleteModule(int $moduleId): void
    {
        CourseModule::findOrFail($moduleId)->delete();
    }

    public function moveModuleUp(int $moduleId): void
    {
        $module = CourseModule::findOrFail($moduleId);
        $prev = CourseModule::where('course_version_id', $module->course_version_id)
            ->where('sort_order', '<', $module->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($prev) {
            $temp = $module->sort_order;
            $module->update(['sort_order' => $prev->sort_order]);
            $prev->update(['sort_order' => $temp]);
        }
    }

    public function moveModuleDown(int $moduleId): void
    {
        $module = CourseModule::findOrFail($moduleId);
        $next = CourseModule::where('course_version_id', $module->course_version_id)
            ->where('sort_order', '>', $module->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($next) {
            $temp = $module->sort_order;
            $module->update(['sort_order' => $next->sort_order]);
            $next->update(['sort_order' => $temp]);
        }
    }

    public function startAddItem(int $moduleId): void
    {
        $this->showItemForm = true;
        $this->showModuleForm = false;
        $this->editingModule = null;
        $this->editingItem = new ModuleItem();
        $this->editingItem->course_module_id = $moduleId;
        $this->moduleSort = $moduleId;
        $this->itemTitle = '';
        $this->itemType = 'video';
        $this->itemRequired = true;
        $this->itemSettings = '';
    }

    public function startEditItem(int $itemId): void
    {
        $item = ModuleItem::with('courseModule')->findOrFail($itemId);
        $this->showItemForm = true;
        $this->showModuleForm = false;
        $this->editingModule = null;
        $this->editingItem = $item;
        $this->moduleSort = $item->course_module_id;
        $this->itemTitle = $item->title;
        $this->itemType = $item->type->value;
        $this->itemRequired = $item->required;
        $this->itemSettings = $item->settings ? json_encode($item->settings, JSON_PRETTY_PRINT) : '';
    }

    public function saveItem(): void
    {
        $this->validate([
            'itemTitle' => ['required', 'string', 'max:255'],
            'itemType' => ['required', 'in:' . implode(',', array_map(fn($case) => $case->value, ModuleItemType::cases()))],
            'itemRequired' => ['boolean'],
            'itemSettings' => ['nullable', 'string', 'max:5000'],
        ]);

        $settings = null;
        if ($this->itemSettings) {
            $decoded = json_decode($this->itemSettings, true);
            $settings = $decoded !== null ? $decoded : $this->itemSettings;
        }

        if ($this->editingItem && $this->editingItem->id) {
            $this->editingItem->update([
                'title' => $this->itemTitle,
                'type' => ModuleItemType::from($this->itemType),
                'required' => $this->itemRequired,
                'settings' => $settings,
            ]);
        } else {
            $moduleId = $this->moduleSort;
            $maxSort = ModuleItem::where('course_module_id', $moduleId)->max('sort_order') ?? 0;

            ModuleItem::create([
                'course_module_id' => $moduleId,
                'title' => $this->itemTitle,
                'type' => ModuleItemType::from($this->itemType),
                'required' => $this->itemRequired,
                'settings' => $settings,
                'sort_order' => $maxSort + 1,
            ]);
        }

        $this->cancelEdit();
    }

    public function deleteItem(int $itemId): void
    {
        ModuleItem::findOrFail($itemId)->delete();
    }

    public function moveItemUp(int $itemId): void
    {
        $item = ModuleItem::findOrFail($itemId);
        $prev = ModuleItem::where('course_module_id', $item->course_module_id)
            ->where('sort_order', '<', $item->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($prev) {
            $temp = $item->sort_order;
            $item->update(['sort_order' => $prev->sort_order]);
            $prev->update(['sort_order' => $temp]);
        }
    }

    public function moveItemDown(int $itemId): void
    {
        $item = ModuleItem::findOrFail($itemId);
        $next = ModuleItem::where('course_module_id', $item->course_module_id)
            ->where('sort_order', '>', $item->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($next) {
            $temp = $item->sort_order;
            $item->update(['sort_order' => $next->sort_order]);
            $next->update(['sort_order' => $temp]);
        }
    }

    public function reorderModules(array $moduleIds): void
    {
        foreach ($moduleIds as $index => $moduleId) {
            CourseModule::where('id', $moduleId)->update(['sort_order' => $index + 1]);
        }
    }

    public function reorderItems(int $moduleId, array $itemIds): void
    {
        foreach ($itemIds as $index => $itemId) {
            ModuleItem::where('id', $itemId)->where('course_module_id', $moduleId)
                ->update(['sort_order' => $index + 1]);
        }
    }

    public function cancelEdit(): void
    {
        $this->showModuleForm = false;
        $this->showItemForm = false;
        $this->editingModule = null;
        $this->editingItem = null;
        $this->moduleTitle = '';
        $this->moduleDescription = '';
        $this->itemTitle = '';
        $this->itemType = 'video';
        $this->itemRequired = true;
        $this->itemSettings = '';
        $this->moduleSort = 0;
        $this->itemSort = 0;
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $course->title }}</h2>
                <p class="text-sm text-gray-500">Curriculum Editor</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.courses.curriculum', $course->slug) }}" wire:navigate
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <x-dynamic-component :component="'lucide-eye'" class="w-4 h-4" />
                    View Curriculum
                </a>
                <span @class([
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    'bg-yellow-100 text-yellow-800' => $course->status === \App\Enums\CourseStatus::DRAFT,
                    'bg-green-100 text-green-800' => $course->status === \App\Enums\CourseStatus::PUBLISHED,
                    'bg-gray-100 text-gray-800' => $course->status === \App\Enums\CourseStatus::ARCHIVED,
                ])>
                    {{ ucfirst($course->status->value) }}
                </span>
                <a href="{{ route('admin.courses.index') }}" wire:navigate
                    class="text-sm text-gray-600 hover:text-gray-900">&larr; Back to Courses</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Edit Course Button (moved inside Livewire root for wire:click to work) --}}
            <div class="flex items-center justify-between">
                <div></div>
                <button wire:click="$toggle('showCourseForm')"
                    class="flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800">
                    <x-dynamic-component :component="'lucide-' . ($showCourseForm ? 'x' : 'pencil')" class="w-4 h-4" />
                    {{ $showCourseForm ? 'Hide Form' : 'Edit Course' }}
                </button>
            </div>
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
            {{-- Course Edit Form --}}
            @if ($showCourseForm)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-400">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Course Details</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" wire:model="courseTitle"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('courseTitle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Slug</label>
                            <input type="text" wire:model="courseSlug"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('courseSlug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea wire:model="courseDescription" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            @error('courseDescription') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select wire:model="courseStatus"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" wire:click="$set('showCourseForm', false)"
                                class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                <x-dynamic-component :component="'lucide-x'" class="w-4 h-4" />
                                Cancel
                            </button>
                            <button type="button" wire:click="saveCourse"
                                class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-500">
                                <x-dynamic-component :component="'lucide-save'" class="w-4 h-4" />
                                Save Course
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Add Module Button --}}
            @if (!$showModuleForm && !$showItemForm)
                <button wire:click="startAddModule"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-500 hover:border-indigo-400 hover:text-indigo-600 transition">
                    <x-dynamic-component :component="'lucide-plus'" class="w-4 h-4" />
                    Add Module
                </button>
            @endif

            {{-- Module Form --}}
            @if ($showModuleForm)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-400">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $editingModule?->id ? 'Edit Module' : 'New Module' }}
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" wire:model="moduleTitle"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('moduleTitle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea wire:model="moduleDescription" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" wire:click="cancelEdit"
                                class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                <x-dynamic-component :component="'lucide-x'" class="w-4 h-4" />
                                Cancel
                            </button>
                            <button type="button" wire:click="saveModule"
                                class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-500">
                                <x-dynamic-component :component="'lucide-save'" class="w-4 h-4" />
                                {{ $editingModule?->id ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Module List --}}
            <div
                x-data
                x-init="
                    new Sortable($el, {
                        animation: 150,
                        handle: '.drag-handle',
                        ghostClass: 'bg-gray-100',
                        dragClass: 'shadow-lg',
                        chosenClass: 'bg-indigo-50',
                        onEnd: (event) => {
                            const ids = [...event.to.children]
                                .map(el => el.dataset.moduleId);

                            $wire.reorderModules(ids);
                        }
                    })
                "
                class="space-y-4">
                @foreach ($this->modules() as $module)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100"
                        wire:key="module-{{ $module->id }}"
                        data-module-id="{{ $module->id }}">
                        <div class="p-4 flex items-center justify-between border-b border-gray-100 bg-gray-50">
                            <div class="flex items-center gap-3">
                                <button type="button"
                                    class="drag-handle p-2 text-gray-300 hover:text-gray-500 cursor-grab active:cursor-grabbing transition-colors"
                                    title="Drag to reorder"
                                    aria-label="Drag to reorder module">
                                    <x-dynamic-component :component="'lucide-grip-vertical'" class="w-5 h-5" />
                                </button>
                                <span class="text-sm font-medium text-gray-400 w-6 text-right">{{ $module->sort_order }}.</span>
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $module->title }}</h3>
                                    @if ($module->description)
                                        <p class="text-sm text-gray-500">{{ $module->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if (!$showModuleForm && !$showItemForm)
                                    <button wire:click="startEditModule({{ $module->id }})" title="Edit"
                                        class="p-1 text-indigo-400 hover:text-indigo-600">
                                        <x-dynamic-component :component="'lucide-pencil'" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="deleteModule({{ $module->id }})"
                                        wire:confirm="Delete this module and all its items?" title="Delete"
                                        class="p-1 text-red-400 hover:text-red-600">
                                        <x-dynamic-component :component="'lucide-trash-2'" class="w-4 h-4" />
                                    </button>
                                @endif
                            </div>
                        </div>

<div class="divide-y divide-gray-100"
                            x-data
                            x-init="
                                new Sortable($el, {
                                    animation: 150,
                                    handle: '.drag-handle',
                                    ghostClass: 'bg-gray-100',
                                    dragClass: 'shadow-lg',
                                    chosenClass: 'bg-indigo-50',
                                    onEnd: (event) => {
                                        const ids = [...event.to.children]
                                            .map(el => el.dataset.itemId);

                                        $wire.reorderItems({{ $module->id }}, ids);
                                    }
                                })
                            ">
                            @foreach ($module->items as $item)
                                <div class="px-4 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors"
                                    wire:key="item-{{ $item->id }}"
                                    data-item-id="{{ $item->id }}">
                                    <div class="flex items-center gap-3 text-sm">
                                        <button type="button"
                                            class="drag-handle p-2 text-gray-300 hover:text-gray-500 cursor-grab active:cursor-grabbing transition-colors"
                                            title="Drag to reorder"
                                            aria-label="Drag to reorder item">
                                            <x-dynamic-component :component="'lucide-grip-vertical'" class="w-4 h-4" />
                                        </button>
                                        <span class="text-xs text-gray-400 w-6 text-right">{{ $item->sort_order }}.</span>
                                        @php
                                            $icon = match ($item->type->value) {
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
                                        <span>{{ $item->title }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-gray-600 bg-gray-100">
                                            {{ ucfirst($item->type->value) }}
                                        </span>
                                        @if (!$item->required)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium text-gray-500 bg-gray-50">
                                                <x-dynamic-component :component="'lucide-flag'" class="w-3 h-3" />
                                                Optional
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium text-indigo-600 bg-indigo-50">
                                                <x-dynamic-component :component="'lucide-check-circle-2'" class="w-3 h-3" />
                                                Required
                                            </span>
                                        @endif
                                    </div>
                                    @if (!$showModuleForm && !$showItemForm)
                                        <div class="flex items-center gap-1">
                                            <button wire:click="startEditItem({{ $item->id }})" title="Edit"
                                                class="p-1.5 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                                <x-dynamic-component :component="'lucide-pencil'" class="w-4 h-4" />
                                            </button>
                                            <button wire:click="deleteItem({{ $item->id }})" wire:confirm="Delete this item?"
                                                title="Delete" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                <x-dynamic-component :component="'lucide-trash-2'" class="w-4 h-4" />
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Add Item Button --}}
                        @if (!$showModuleForm && !$showItemForm)
                            <div class="px-4 py-2 border-t border-dashed border-gray-200">
                                <button wire:click="startAddItem({{ $module->id }})"
                                    class="flex items-center gap-1.5 text-sm text-indigo-500 hover:text-indigo-700">
                                    <x-dynamic-component :component="'lucide-plus'" class="w-4 h-4" />
                                    Add Item
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Item Form --}}
            @if ($showItemForm)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-emerald-400">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $editingItem?->id ? 'Edit Item' : 'New Item' }}
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" wire:model="itemTitle"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('itemTitle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <select wire:model="itemType"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="video">Video</option>
                                <option value="pdf">PDF</option>
                                <option value="article">Article</option>
                                <option value="quiz">Quiz</option>
                                <option value="assignment">Assignment</option>
                                <option value="survey">Survey</option>
                                <option value="external_link">External Link</option>
                                <option value="custom_page">Custom Page</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="itemRequired" wire:model="itemRequired"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="itemRequired" class="text-sm font-medium text-gray-700">Required</label>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Settings (JSON)</label>
                            <textarea wire:model="itemSettings" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-xs"
                                placeholder='{"youtube_id": "abc123", "duration": 147}'></textarea>
                            <p class="mt-1 text-xs text-gray-500">Optional JSON configuration for this item.</p>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" wire:click="cancelEdit"
                                class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                <x-dynamic-component :component="'lucide-x'" class="w-4 h-4" />
                                Cancel
                            </button>
                            <button type="button" wire:click="saveItem"
                                class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-md hover:bg-emerald-500">
                                <x-dynamic-component :component="'lucide-save'" class="w-4 h-4" />
                                {{ $editingItem?->id ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if ($this->modules()->isEmpty() && !$showModuleForm && !$showItemForm)
                <div class="text-center py-12 text-gray-500">
                    No modules yet. Click "Add Module" above to build your course.
                </div>
            @endif

        </div>
    </div>
</div>