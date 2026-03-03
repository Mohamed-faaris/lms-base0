<?php

namespace App\Livewire\Faculty;

use Livewire\Component;

class CoursePlayer extends Component
{
    public function render()
    {
        return view('livewire.faculty.course-player')->layout('layouts.app');
    }
}
