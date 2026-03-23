<?php

namespace App\Livewire\Admin\Courses;

use App\Enums\College;
use App\Enums\Department;
use App\Enums\Role;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Enroll extends Component
{
    public Course $course;

    public array $selectedColleges = [];

    public array $selectedDepartments = [];

    public array $selectedFacultyIds = [];

    public array $collegeOptions = [];

    public array $departmentOptions = [];

    public array $facultyOptions = [];

    public array $collegeCounts = [];

    public array $departmentCounts = [];

    public int $facultyCount = 0;

    public string $deadlineDays = '30';

    public array $eligibleUsers = [];

    public int $enrollmentCount = 0;

    public bool $showSuccess = false;

    public function selectAll(string $property): void
    {
        $this->{$property} = $this->allOptionsFor($property);
        $this->loadEligibleUsers();
    }

    public function toggleSelection(string $property, string $value): void
    {
        $items = $this->{$property} ?? [];
        $allOptions = $this->allOptionsFor($property);

        if ($value === '__all__') {
            $this->{$property} = $allOptions;
            $this->loadEligibleUsers();

            return;
        }

        if (count($items) === count($allOptions)) {
            $this->{$property} = [$value];
            $this->loadEligibleUsers();

            return;
        }

        if (in_array($value, $items, true)) {
            $this->{$property} = array_values(array_filter($items, fn ($item) => $item !== $value));

            if (empty($this->{$property})) {
                $this->{$property} = $allOptions;
            }

            $this->loadEligibleUsers();

            return;
        }

        $this->{$property}[] = $value;
        $this->{$property} = array_values(array_unique($this->{$property}));

        if (count($this->{$property}) === count($allOptions)) {
            $this->{$property} = $allOptions;
        }

        $this->loadEligibleUsers();
    }

    public function mount(Course $course): void
    {
        if (! Auth::user()->isAdmin()) {
            abort(403);
        }

        $this->course = Course::findOrFail($course->id);
        $this->collegeOptions = array_map(
            static fn (College $college): string => $college->value,
            College::cases(),
        );
        $this->departmentOptions = array_map(
            static fn (Department $department): string => $department->value,
            Department::cases(),
        );
        $this->facultyOptions = User::query()
            ->where('role', Role::Faculty)
            ->orderBy('name')
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $this->collegeCounts = User::query()
            ->where('role', Role::Staff)
            ->selectRaw('college, count(*) as total')
            ->groupBy('college')
            ->pluck('total', 'college')
            ->map(fn ($count) => (int) $count)
            ->all();

        $this->departmentCounts = User::query()
            ->where('role', Role::Staff)
            ->selectRaw('department, count(*) as total')
            ->groupBy('department')
            ->pluck('total', 'department')
            ->map(fn ($count) => (int) $count)
            ->all();

        $this->facultyCount = count($this->facultyOptions);

        $this->selectedColleges = $this->collegeOptions;
        $this->selectedDepartments = $this->departmentOptions;
        $this->selectedFacultyIds = $this->facultyOptions;

        $this->loadEligibleUsers();
    }

    public function updatedSelectedColleges(): void
    {
        $this->loadEligibleUsers();
    }

    public function updatedSelectedDepartments(): void
    {
        $this->loadEligibleUsers();
    }

    public function updatedSelectedFacultyIds(): void
    {
        $this->loadEligibleUsers();
    }

    public function loadEligibleUsers(): void
    {
        $collegeQuery = User::query()->where('role', Role::Staff);
        if (! empty($this->selectedColleges) && count($this->selectedColleges) < count($this->collegeOptions)) {
            $collegeQuery->whereIn('college', $this->selectedColleges);
        }

        $departmentQuery = User::query()->where('role', Role::Staff);
        if (! empty($this->selectedDepartments) && count($this->selectedDepartments) < count($this->departmentOptions)) {
            $departmentQuery->whereIn('department', $this->selectedDepartments);
        }

        $facultyQuery = User::query()->where('role', Role::Faculty);
        if (! empty($this->selectedFacultyIds) && count($this->selectedFacultyIds) < count($this->facultyOptions)) {
            $facultyQuery->whereIn('id', array_map('intval', $this->selectedFacultyIds));
        }

        $userIds = $collegeQuery->pluck('id')
            ->merge($departmentQuery->pluck('id'))
            ->merge($facultyQuery->pluck('id'))
            ->unique()
            ->values()
            ->all();

        $users = User::query()
            ->whereIn('id', $userIds)
            ->orderBy('name')
            ->get();

        $enrolledUserIds = Enrollment::where('course_id', $this->course->id)
            ->pluck('user_id')
            ->all();

        $this->eligibleUsers = $users
            ->reject(fn (User $user): bool => in_array($user->id, $enrolledUserIds, true))
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'college' => $user->college?->label() ?? 'N/A',
                'department' => $user->department?->label() ?? 'N/A',
            ])
            ->values()
            ->all();

        $this->enrollmentCount = count($this->eligibleUsers);
    }

    public function isAllSelected(string $property): bool
    {
        return count($this->{$property} ?? []) === count($this->allOptionsFor($property));
    }

    public function allOptionsFor(string $property): array
    {
        return match ($property) {
            'selectedColleges' => $this->collegeOptions,
            'selectedDepartments' => $this->departmentOptions,
            'selectedFacultyIds' => $this->facultyOptions,
            default => [],
        };
    }

    public function countForOption(string $property, string $value): int
    {
        return match ($property) {
            'selectedColleges' => $this->collegeCounts[$value] ?? 0,
            'selectedDepartments' => $this->departmentCounts[$value] ?? 0,
            'selectedFacultyIds' => 1,
            default => 0,
        };
    }

    public function enrollUsers(): void
    {
        $this->validate([
            'deadlineDays' => ['required', 'integer', 'min:1'],
            'selectedColleges' => ['array'],
            'selectedColleges.*' => ['string', Rule::in(array_column(College::cases(), 'value'))],
            'selectedDepartments' => ['array'],
            'selectedDepartments.*' => ['string', Rule::in(array_column(Department::cases(), 'value'))],
            'selectedFacultyIds' => ['array'],
            'selectedFacultyIds.*' => ['integer', 'exists:users,id'],
        ]);

        $deadline = now()->addDays((int) $this->deadlineDays)->timestamp;
        $enrolledBy = Auth::id();

        foreach ($this->eligibleUsers as $user) {
            Enrollment::firstOrCreate(
                [
                    'user_id' => $user['id'],
                    'course_id' => $this->course->id,
                ],
                [
                    'enrolled_by' => $enrolledBy,
                    'deadline' => $deadline,
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
