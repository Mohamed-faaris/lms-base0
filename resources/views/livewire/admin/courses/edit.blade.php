<div>
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <flux:button variant="ghost" href="{{ route('admin.courses.show', $course->id) }}" wire:navigate icon="arrow-left" />
        <flux:heading level="1" size="xl">Edit Course</flux:heading>
    </div>

    {{-- Form --}}
    <div class="max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            {{-- Title --}}
            <flux:field>
                <flux:label>Title</flux:label>
                <flux:input 
                    wire:model="title" 
                    placeholder="Enter course title" 
                    required
                />
                <flux:error name="title" />
            </flux:field>

            {{-- Slug --}}
            <flux:field>
                <flux:label>Slug</flux:label>
                <flux:input 
                    wire:model="slug" 
                    placeholder="Auto-generated from title"
                />
                <p class="text-sm text-zinc-500 dark:text-zinc-400">URL-friendly version of the title</p>
            </flux:field>

            {{-- Description --}}
            <flux:field>
                <flux:label>Description</flux:label>
                <flux:textarea 
                    wire:model="description" 
                    placeholder="Enter course description"
                    rows="4"
                />
            </flux:field>

            {{-- Actions --}}
            <div class="flex gap-4">
                <flux:button type="submit" variant="primary">
                    Save Changes
                </flux:button>
                <flux:button href="{{ route('admin.courses.show', $course->id) }}" wire:navigate variant="outline">
                    Cancel
                </flux:button>
            </div>
        </form>
    </div>
</div>