<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Dashboard extends Component
{
    public function mount(): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard')->layout('layouts.app');
    }
}
