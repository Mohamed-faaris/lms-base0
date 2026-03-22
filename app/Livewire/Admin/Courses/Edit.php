<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    public Course $course;

    public string $title = '';

    public string $slug = '';

    public string $description = '';

    public string $status = 'draft';

    public function mount(Course $course): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $this->course = Course::findOrFail($course->id);
        $this->title = $this->course->title;
        $this->slug = $this->course->slug ?? '';
        $this->description = $this->course->description ?? '';
    }

    public function updatedTitle(string $value): void
    {
        if (empty($this->slug) || $this->slug === Str::slug($this->course->title)) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:courses,slug,'.$this->course->id,
            'description' => 'nullable|string',
        ]);

        $this->course->update([
            'title' => $this->title,
            'slug' => $this->slug ?: Str::slug($this->title),
            'description' => $this->description,
        ]);

        session()->flash('success', 'Course updated successfully.');

        $this->redirectRoute('admin.courses.show', $this->course->id);
    }

    public function render()
    {
        return view('livewire.admin.courses.edit');
    }
}
