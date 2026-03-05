<?php

namespace App\Livewire\Faculty;

use Livewire\Component;

class Profile extends Component
{
    public function render()
    {
        return view('livewire.faculty.profile')->layout('layouts.app');
    }
}
