<?php

?>

<div class="flex items-center justify-end gap-2">
    <flux:button variant="ghost" size="sm" href="{{ route('admin.courses.show', $course->id) }}" wire:navigate>
        View
    </flux:button>
    <flux:button variant="ghost" size="sm" href="{{ route('admin.courses.edit', $course->id) }}" wire:navigate>
        Edit
    </flux:button>
    <flux:button variant="ghost" size="sm" wire:click="deleteCourse({{ $course->id }})" onclick="return confirm('Are you sure you want to delete this course?')">
        <span class="text-red-600">Delete</span>
    </flux:button>
</div>