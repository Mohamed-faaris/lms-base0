<div>
    <div class="flex items-center gap-4 mb-6">
        <flux:button variant="ghost" href="{{ route('admin.courses.show', $course?->id) }}" wire:navigate icon="arrow-left" />
        <div class="flex-1">
            <flux:heading level="1" size="xl">{{ $content ? 'Edit' : 'Create' }} Content</flux:heading>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                {{ $course?->title ?? 'Select a course' }}
            </p>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
        <form wire:submit="save" class="space-y-6">
            <flux:field>
                <flux:label>Module</flux:label>
                @if ($content)
                    <flux:select wire:model="selectedModuleId" required>
                        @foreach ($availableModules as $module)
                            <flux:select.option value="{{ $module['id'] }}" {{ $module['id'] === $selectedModuleId ? 'selected' : '' }}>
                                {{ $module['title'] }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                @else
                    <flux:select wire:model="selectedModuleId" required>
                        <flux:select.option value="">Select a module</flux:select.option>
                        @foreach ($availableModules as $module)
                            <flux:select.option value="{{ $module['id'] }}">
                                {{ $module['title'] }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                @endif
                <flux:error name="selectedModuleId" />
            </flux:field>

            <flux:field>
                <flux:label>Title</flux:label>
                <flux:input wire:model="title" placeholder="Content title" required />
                <flux:error name="title" />
            </flux:field>

            <flux:field>
                <flux:label>Type</flux:label>
                <flux:select wire:model="type" required>
                    <flux:select.option value="video">Video</flux:select.option>
                    <flux:select.option value="article">Article</flux:select.option>
                    <flux:select.option value="quiz">Quiz</flux:select.option>
                </flux:select>
                <flux:error name="type" />
            </flux:field>

            <flux:field>
                <flux:label>Body</flux:label>
                <flux:textarea wire:model="body" placeholder="Content body (optional)" />
                <flux:error name="body" />
            </flux:field>

            <flux:field>
                <flux:label>URL</flux:label>
                <flux:input wire:model="contentUrl" placeholder="Content URL (optional)" />
                <flux:error name="contentUrl" />
            </flux:field>

            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="outline" href="{{ route('admin.courses.show', $course?->id) }}" wire:navigate>
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $content ? 'Update' : 'Create' }} Content
                </flux:button>
            </div>
        </form>
    </div>
</div>
