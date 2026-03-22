<div>
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <flux:button variant="ghost" href="{{ route('admin.courses.index') }}" wire:navigate icon="arrow-left" />
        <div class="flex-1">
            <flux:heading level="1" size="xl">{{ $course->title }}</flux:heading>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">/{{ $course->slug }}</p>
        </div>
        <flux:button href="{{ route('admin.courses.edit', $course->id) }}" wire:navigate>
            Edit
        </flux:button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid gap-4 md:grid-cols-4 mb-8">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                    <flux:icon.users class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $totalEnrollments }}</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Enrollments</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-green-500/20 flex items-center justify-center">
                    <flux:icon.check-circle class="h-5 w-5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $completedEnrollments }}</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Completed</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                    <flux:icon.chart-bar class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $avgProgress }}%</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Avg Progress</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-orange-500/20 flex items-center justify-center">
                    <flux:icon.document-text class="h-5 w-5 text-orange-600 dark:text-orange-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $totalContent }}</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Content Items</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Overview --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6 mb-6">
        <flux:heading level="2" size="lg" class="mb-4">Overview</flux:heading>
        <p class="text-zinc-600 dark:text-zinc-300">
            {{ $course->description ?: 'No description provided.' }}
        </p>
        <div class="mt-4 flex gap-4 text-sm text-zinc-500 dark:text-zinc-400">
            <span>Created: {{ $course->created_at->format('M d, Y') }}</span>
            <span>Updated: {{ $course->updated_at->format('M d, Y') }}</span>
        </div>
    </div>

    {{-- Topics, Modules & Content --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
        <div class="flex items-center justify-between mb-4">
            <flux:heading level="2" size="lg">Course Structure</flux:heading>
            <flux:button size="sm" wire:click="openTopicModal()" icon="plus">
                Add Topic
            </flux:button>
        </div>

        @forelse ($course->topics as $topic)
            <div class="mb-4 last:mb-0">
                {{-- Topic Header --}}
                <div class="flex items-center gap-2">
                    <button
                        wire:click="toggleTopic({{ $topic->id }})"
                        class="flex-1 flex items-center justify-between px-4 py-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <flux:icon.chevron-down class="w-5 h-5 {{ !($expandedTopics[$topic->id] ?? true) ? '-rotate-90' : '' }} transition-transform" />
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $topic->name }}</h3>
                            <flux:badge color="zinc" size="sm">{{ $topic->modules->count() }} modules</flux:badge>
                        </div>
                    </button>
                    <flux:button size="sm" variant="ghost" wire:click="openModuleModal(null); selectedTopicId = {{ $topic->id }}">
                        <flux:icon.plus variant="mini" />
                    </flux:button>
                </div>

                {{-- Modules --}}
                @if ($expandedTopics[$topic->id] ?? true)
                    <div class="ml-4 mt-2 space-y-2">
                        @forelse ($topic->modules as $module)
                            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden">
                                {{-- Module Header --}}
                                <div class="flex items-center gap-2">
                                    <button
                                        wire:click="toggleModule({{ $module->id }})"
                                        class="flex-1 flex items-center justify-between px-4 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors"
                                    >
                                        <div class="flex items-center gap-3">
                                            <flux:icon.chevron-down class="w-4 h-4 {{ !($expandedModules[$module->id] ?? true) ? '-rotate-90' : '' }} transition-transform" />
                                            <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $module->title }}</span>
                                            <flux:badge color="zinc" size="xs">{{ $module->contents->count() }} items</flux:badge>
                                        </div>
                                    </button>
                                    <flux:button size="sm" variant="ghost" wire:click="openContentModal(null); selectedModuleId = {{ $module->id }}">
                                        <flux:icon.plus variant="mini" />
                                    </flux:button>
                                </div>

                                {{-- Content Items --}}
                                @if ($expandedModules[$module->id] ?? true)
                                    <div class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                                        @forelse ($module->contents as $content)
                                            <button
                                                wire:click="openViewContentModal({{ $content->id }})"
                                                class="w-full flex items-center justify-between px-4 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors text-left"
                                            >
                                                <div class="flex items-center gap-3">
                                                    @switch($content->type)
                                                        @case('video')
                                                            <flux:icon.video-camera class="w-4 h-4 text-blue-500" />
                                                            @break
                                                        @case('quiz')
                                                            <flux:icon.clipboard-document-check class="w-4 h-4 text-green-500" />
                                                            @break
                                                        @case('article')
                                                            <flux:icon.document class="w-4 h-4 text-purple-500" />
                                                            @break
                                                        @default
                                                            <flux:icon.document-text class="w-4 h-4 text-zinc-500" />
                                                    @endswitch
                                                    <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $content->title }}</span>
                                                </div>
                                                <span class="text-xs text-zinc-400">{{ $content->type }}</span>
                                            </button>
                                        @empty
                                            <div class="px-4 py-2 text-sm text-zinc-500">No content items</div>
                                        @endforelse
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-zinc-500 ml-4">No modules.</p>
                        @endforelse
                    </div>
                @endif
            </div>
        @empty
            <p class="text-zinc-500 dark:text-zinc-400">No topics or modules yet.</p>
        @endforelse
    </div>

    {{-- Topic Modal --}}
    <flux:modal wire:model="showTopicModal">
        <flux:heading>
            {{ $editingTopic ? 'Edit Topic' : 'Add Topic' }}
        </flux:heading>

        <form wire:submit="saveTopic" class="space-y-4 mt-4">
            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input wire:model="topicName" placeholder="Topic name" required />
            </flux:field>

            <flux:field>
                <flux:label>Description</flux:label>
                <flux:textarea wire:model="topicDescription" placeholder="Optional description" />
            </flux:field>

            <flux:field>
                <flux:label>Order</flux:label>
                <flux:input wire:model="topicOrder" type="number" min="1" required />
            </flux:field>

            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="outline" wire:click="closeTopicModal">Cancel</flux:button>
                <flux:button type="submit" variant="primary">{{ $editingTopic ? 'Update' : 'Create' }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Module Modal --}}
    <flux:modal wire:model="showModuleModal">
        <flux:heading>
            {{ $editingModule ? 'Edit Module' : 'Add Module' }}
        </flux:heading>

        <form wire:submit="saveModule" class="space-y-4 mt-4">
            <flux:field>
                <flux:label>Topic</flux:label>
                @if ($editingModule)
                    <flux:select wire:model="selectedTopicId" required>
                        @foreach ($course->topics as $topic)
                            <flux:select.option value="{{ $topic->id }}" {{ $topic->id === $selectedTopicId ? 'selected' : '' }}>{{ $topic->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                @else
                    <flux:input :value="$course->topics->firstWhere('id', $selectedTopicId)?->name" disabled />
                    <input type="hidden" wire:model="selectedTopicId" />
                @endif
            </flux:field>

            <flux:field>
                <flux:label>Title</flux:label>
                <flux:input wire:model="moduleTitle" placeholder="Module title" required />
            </flux:field>

            <flux:field>
                <flux:label>Description</flux:label>
                <flux:textarea wire:model="moduleDescription" placeholder="Optional description" />
            </flux:field>

            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="outline" wire:click="closeModuleModal">Cancel</flux:button>
                <flux:button type="submit" variant="primary">{{ $editingModule ? 'Update' : 'Create' }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Content Modal --}}
    <flux:modal wire:model="showContentModal">
        <flux:heading>
            {{ $editingContent ? 'Edit Content' : 'Add Content' }}
        </flux:heading>

        <form wire:submit="saveContent" class="space-y-4 mt-4">
            <flux:field>
                <flux:label>Module</flux:label>
                @if ($editingContent)
                    <flux:select wire:model="selectedModuleId" required>
                        @foreach ($course->topics->flatMap->modules as $module)
                            <flux:select.option value="{{ $module->id }}" {{ $module->id === $selectedModuleId ? 'selected' : '' }}>{{ $module->title }}</flux:select.option>
                        @endforeach
                    </flux:select>
                @else
                    @php
                        $selectedModule = $course->topics->flatMap->modules->firstWhere('id', $selectedModuleId);
                    @endphp
                    <flux:input :value="$selectedModule?->title" disabled />
                    <input type="hidden" wire:model="selectedModuleId" />
                @endif
            </flux:field>

            <flux:field>
                <flux:label>Title</flux:label>
                <flux:input wire:model="contentTitle" placeholder="Content title" required />
            </flux:field>

            <flux:field>
                <flux:label>Type</flux:label>
                <flux:select wire:model="contentType" required>
                    <flux:select.option value="video">Video</flux:select.option>
                    <flux:select.option value="article">Article</flux:select.option>
                    <flux:select.option value="quiz">Quiz</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Body</flux:label>
                <flux:textarea wire:model="contentBody" placeholder="Content body (optional)" />
            </flux:field>

            <flux:field>
                <flux:label>URL</flux:label>
                <flux:input wire:model="contentUrl" placeholder="Content URL (optional)" />
            </flux:field>

            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="outline" wire:click="closeContentModal">Cancel</flux:button>
                <flux:button type="submit" variant="primary">{{ $editingContent ? 'Update' : 'Create' }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- View Content Modal --}}
    <flux:modal wire:model="showViewContentModal">
        @if ($viewingContent)
            <flux:heading>{{ $viewingContent->title }}</flux:heading>

            <div class="mt-4 space-y-4">
                <div class="flex items-center gap-2">
                    <flux:badge>
                        {{ $viewingContent->type }}
                    </flux:badge>
                    <span class="text-sm text-zinc-500">Order: {{ $viewingContent->order }}</span>
                </div>

                @if ($viewingContent->content_url)
                    <div>
                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">URL</p>
                        <a href="{{ $viewingContent->content_url }}" target="_blank" class="text-sm text-blue-600 hover:underline">
                            {{ $viewingContent->content_url }}
                        </a>
                    </div>
                @endif

                @if ($viewingContent->body)
                    <div>
                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Body</p>
                        <div class="mt-1 p-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $viewingContent->body }}
                        </div>
                    </div>
                @endif

                <div class="flex justify-between pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button type="button" variant="outline" wire:click="openContentModal({{ $viewingContent->id }})">
                        Edit Content
                    </flux:button>
                    <flux:button type="button" wire:click="closeViewContentModal">Close</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
