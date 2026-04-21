<?php

namespace App\Livewire\Manager;

use App\Models\Enrollment;
use App\Models\Progress;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    /** @var array<string, int> */
    public array $stats = [];

    /** @var list<array<string, mixed>> */
    public array $recentFaculty = [];

    /** @var list<string> */
    public array $scopeLabels = [];

    public function mount(): void
    {
        abort_unless(auth()->user()?->isManager(), 403);

        $this->loadDashboard();
    }

    public function render(): View
    {
        return view('livewire.manager.dashboard')->layout('layouts.app');
    }

    protected function loadDashboard(): void
    {
        /** @var User $manager */
        $manager = auth()->user();
        $facultyIds = $manager->scopedFacultyQuery()->pluck('users.id');

        $this->scopeLabels = $manager->managerScopes()
            ->get()
            ->map(fn ($scope): string => $scope->label())
            ->all();

        $this->stats = [
            'assignedScopes' => count($this->scopeLabels),
            'facultyUsers' => $facultyIds->count(),
            'activeEnrollments' => Enrollment::query()->whereIn('user_id', $facultyIds)->count(),
            'recentCompletions' => Progress::query()
                ->whereIn('user_id', $facultyIds)
                ->where('completed_at', '>=', now()->subDays(7))
                ->count(),
        ];

        $this->recentFaculty = $manager->scopedFacultyQuery()
            ->latest('users.created_at')
            ->limit(6)
            ->get()
            ->map(fn (User $faculty): array => [
                'name' => $faculty->name,
                'email' => $faculty->email,
                'college' => $faculty->college?->label() ?? 'N/A',
                'department' => $faculty->department?->label() ?? 'N/A',
                'profileUrl' => route('manager.faculty.profile', $faculty),
            ])
            ->all();
    }
}
