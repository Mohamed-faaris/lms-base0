<div class="flex flex-wrap gap-2">
    <flux:button
        href="{{ route('admin.courses.show', $course->id) }}"
        wire:navigate
        :variant="request()->routeIs('admin.courses.show') ? 'primary' : 'outline'"
        size="sm"
    >
        View
    </flux:button>
    <flux:button
        href="{{ route('admin.courses.edit', $course->id) }}"
        wire:navigate
        :variant="request()->routeIs('admin.courses.edit') ? 'primary' : 'outline'"
        size="sm"
    >
        Edit
    </flux:button>
    <flux:button href="{{ route('admin.courses.analyze', $course->id) }}" wire:navigate variant="ghost" size="sm">
        Analyze
    </flux:button>
    <flux:button href="{{ route('admin.enrollments.create', ['course' => $course->slug]) }}" wire:navigate variant="ghost" size="sm">
        Enroll
    </flux:button>
</div>
