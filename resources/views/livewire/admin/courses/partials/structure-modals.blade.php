{{-- Topic Modal --}}
<flux:modal wire:model="showTopicModal" size="lg">
    <flux:heading>{{ $editingTopic ? 'Edit Topic' : 'Add Topic' }}</flux:heading>

    <form wire:submit="saveTopic" class="mt-4 space-y-4">
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
<flux:modal wire:model="showModuleModal" size="lg">
    <flux:heading>{{ $editingModule ? 'Edit Module' : 'Add Module' }}</flux:heading>

    <form wire:submit="saveModule" class="mt-4 space-y-4">
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
<flux:modal wire:model="showContentModal" size="lg">
    <flux:heading>{{ $editingContent ? 'Edit Content' : 'Add Content' }}</flux:heading>

    <form wire:submit="saveContent" class="mt-4 space-y-4">
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
                <flux:select.option value="ppt">Presentation</flux:select.option>
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
