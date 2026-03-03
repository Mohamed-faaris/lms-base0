<?php

namespace App\Livewire\Faculty;

use Livewire\Component;

class Notifications extends Component
{
    public function render()
    {
        return view('livewire.faculty.notifications')->layout('layouts.app');
    }
}
