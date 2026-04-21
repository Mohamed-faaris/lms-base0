<?php

namespace App\Livewire\Manager\Faculty;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    /** @var list<array<string, mixed>> */
    public array $faculty = [];

    /** @var list<string> */
    public array $scopeLabels = [];

    public function mount(): void
    {
        abort_unless(auth()->user()?->isManager(), 403);

        /** @var User $manager */
        $manager = auth()->user();

        $this->scopeLabels = $manager->managerScopes()
            ->get()
            ->map(fn ($scope): string => $scope->label())
            ->all();

        $this->faculty = $manager->scopedFacultyQuery()
            ->withCount('enrollments')
            ->orderBy('name')
            ->get()
            ->map(fn (User $faculty): array => [
                'name' => $faculty->name,
                'email' => $faculty->email,
                'college' => $faculty->college?->label() ?? 'N/A',
                'department' => $faculty->department?->label() ?? 'N/A',
                'enrollmentsCount' => $faculty->enrollments_count,
                'profileUrl' => route('manager.faculty.profile', $faculty),
            ])
            ->all();
    }

    public function render(): View
    {
        return view('livewire.manager.faculty.index')->layout('layouts.app');
    }
}
