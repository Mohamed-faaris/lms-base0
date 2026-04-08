<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use Illuminate\Support\Str;
use Livewire\Component;

class Create extends Component
{
    public string $title = '';

    public string $slug = '';

    public string $description = '';

    public string $status = 'draft';

    public function mount(): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }
    }

    public function updatedTitle(string $value): void
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:courses,slug',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,published',
        ]);

        Course::create([
            'title' => $this->title,
            'slug' => $this->slug ?: Str::slug($this->title),
            'description' => $this->description,
        ]);

        session()->flash('success', 'Course created successfully.');

        $this->redirectRoute('admin.courses.index');
    }

    public function render()
    {
        return view('livewire.admin.courses.create');
    }
}
