<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function mount(): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $users = User::query()
            ->with('meta')
            ->whereNotIn('role', ['admin', 'superAdmin'])
            ->when($this->search !== '', function ($builder): void {
                $builder->where(function ($searchQuery): void {
                    $searchQuery
                        ->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.admin.users.index', [
            'adminUsers' => User::query()->whereIn('role', ['admin', 'superAdmin'])->count(),
            'facultyUsers' => User::query()->where('role', 'faculty')->count(),
            'totalUsers' => User::count(),
            'users' => $users,
        ])->layout('layouts.app');
    }
}
