<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = 'all';

    public function mount(): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }
    }

    public function render()
    {
        $query = Course::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('slug', 'like', "%{$this->search}%");
            });
        }

        $courses = $query->withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.courses.index', [
            'courses' => $courses,
        ]);
    }

    public function deleteCourse(int $id): void
    {
        $course = Course::findOrFail($id);
        $course->delete();

        session()->flash('success', 'Course deleted successfully.');
    }
}
