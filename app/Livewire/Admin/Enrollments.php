<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Enrollments extends Component
{
    public string $search = '';

    public function mount(): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }
    }

    public function render(): View
    {
        $courses = Course::orderBy('title')->get();

        return view('livewire.admin.enrollments', [
            'courses' => $courses,
        ]);
    }
}
