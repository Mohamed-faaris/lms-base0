<?php

namespace App\Livewire\Admin\Courses;

use App\Enums\College;
use App\Enums\Department;
use App\Enums\Role;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Enroll extends Component
{
    public Course $course;

    public string $enrollType = 'college';

    public ?College $selectedCollege = null;

    public ?Department $selectedDepartment = null;

    public ?User $selectedUser = null;

    public string $deadlineDays = '30';

    public array $eligibleUsers = [];

    public int $enrollmentCount = 0;

    public bool $showSuccess = false;

    public function mount(Course $course): void
    {
        if (! Auth::user()->isAdmin()) {
            abort(403);
        }

        $this->course = Course::findOrFail($course->id);
        $this->loadEligibleUsers();
    }

    public function updatedEnrollType(): void
    {
        $this->selectedCollege = null;
        $this->selectedDepartment = null;
        $this->selectedUser = null;
        $this->eligibleUsers = [];
        $this->enrollmentCount = 0;
    }

    public function updatedSelectedCollege(): void
    {
        $this->selectedDepartment = null;
        $this->selectedUser = null;
        $this->loadEligibleUsers();
    }

    public function updatedSelectedDepartment(): void
    {
        $this->selectedUser = null;
        $this->loadEligibleUsers();
    }

    public function updatedSelectedUser(): void
    {
        $this->loadEligibleUsers();
    }

    protected function loadEligibleUsers(): void
    {
        $query = User::where('role', Role::Faculty);

        if ($this->enrollType === 'college' && $this->selectedCollege) {
            $query->where('college', $this->selectedCollege);
        }

        if ($this->enrollType === 'department' && $this->selectedCollege && $this->selectedDepartment) {
            $query->where('college', $this->selectedCollege)
                ->where('department', $this->selectedDepartment);
        }

        if ($this->enrollType === 'individual' && $this->selectedUser) {
            $query->where('id', $this->selectedUser->id);
        }

        $users = $query->orderBy('name')->get();

        $enrolledUserIds = Enrollment::where('course_id', $this->course->id)
            ->pluck('user_id')
            ->toArray();

        $this->eligibleUsers = $users->filter(function ($user) use ($enrolledUserIds) {
            return ! in_array($user->id, $enrolledUserIds);
        })->values()->toArray();

        $this->enrollmentCount = count($this->eligibleUsers);
    }

    public function enrollUsers(): void
    {
        $days = (int) $this->deadlineDays;
        $deadline = now()->addDays($days);

        $enrolledBy = auth()->id();

        foreach ($this->eligibleUsers as $user) {
            Enrollment::firstOrCreate(
                ['user_id' => $user['id'], 'course_id' => $this->course->id],
                [
                    'enrolled_by' => $enrolledBy,
                    'deadline' => $deadline->timestamp,
                    'enrolled_at' => now(),
                ]
            );
        }

        $this->showSuccess = true;
        $this->loadEligibleUsers();
    }

    public function render()
    {
        return view('livewire.admin.courses.enroll');
    }
}
