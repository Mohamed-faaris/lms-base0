<?php

namespace App\Livewire\Admin\Users;

use App\Enums\College;
use App\Enums\Department;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
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
        return view('livewire.admin.users.index', [
            'adminUsers' => User::query()->whereIn('role', ['admin', 'superAdmin'])->count(),
            'facultyUsers' => User::query()->where('role', 'faculty')->count(),
            'totalUsers' => User::count(),
            'colleges' => College::cases(),
            'departments' => Department::cases(),
        ]);
    }
}
