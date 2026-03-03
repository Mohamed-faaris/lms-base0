<?php

namespace App\Livewire\Faculty;

use Livewire\Component;

class Courses extends Component
{
    public function render()
    {
        return view('livewire.faculty.courses')->layout('layouts.app');
    }
}
