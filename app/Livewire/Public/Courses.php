<?php

namespace App\Livewire\Public;

use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;

class Courses extends Component
{
    use WithPagination;

    public string $search = '';

    public function render()
    {
        $query = Course::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        $courses = $query->withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('livewire.public.courses', [
            'courses' => $courses,
        ]);
    }
}
