<?php

namespace App\Livewire\Admin\Enrollments;

use App\Enums\College;
use App\Enums\Department;
use App\Enums\Role;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\PostHogService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;

class Create extends Component
{
    #[Url(as: 'course')]
    public ?string $courseSlug = null;

    public ?int $courseId = null;

    public string $courseSearch = '';

    public string $targetMode = 'all';

    public array $selectedColleges = [];

    public array $selectedDepartments = [];

    public array $selectedUserIds = [];

    public string $userSearch = '';

    public string $deadlineDays = '30';

    public array $previewUsers = [];

    public int $resolvedUserCount = 0;

    public int $skippedDuplicateCount = 0;

    public bool $courseLocked = false;

    public bool $showSuccess = false;

    public ?string $createdBatchId = null;

    public int $createdCount = 0;

    public int $skippedCount = 0;

    public function mount(?Course $course = null): void
    {
        if (! Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($course !== null) {
            $this->courseId = $course->id;
            $this->courseLocked = $course !== null;
        } elseif (filled($this->courseSlug)) {
            $this->syncCourseSelectionFromSlug();
        }

        $this->refreshPreview();
    }

    public function updatedCourseId(): void
    {
        $this->showSuccess = false;
        if (! $this->courseLocked) {
            $this->courseSlug = $this->selectedCourse()?->slug;
        }
        $this->refreshPreview();
    }

    public function updatedCourseSlug(): void
    {
        if ($this->courseLocked) {
            return;
        }

        $this->syncCourseSelectionFromSlug();
        $this->refreshPreview();
    }

    public function updatedTargetMode(): void
    {
        $this->selectedColleges = [];
        $this->selectedDepartments = [];
        $this->selectedUserIds = [];
        $this->userSearch = '';
        $this->showSuccess = false;
        $this->refreshPreview();
    }

    public function updatedSelectedDepartments(): void
    {
        $this->showSuccess = false;
        $this->refreshPreview();
    }

    public function updatedSelectedUserIds(): void
    {
        $this->showSuccess = false;
        $this->refreshPreview();
    }

    public function updatedDeadlineDays(): void
    {
        $this->showSuccess = false;
    }

    public function toggleCollege(string $college): void
    {
        if (in_array($college, $this->selectedColleges, true)) {
            $this->selectedColleges = array_values(array_filter(
                $this->selectedColleges,
                fn (string $selectedCollege): bool => $selectedCollege !== $college,
            ));
        } else {
            $this->selectedColleges[] = $college;
            $this->selectedColleges = array_values(array_unique($this->selectedColleges));
        }

        $this->showSuccess = false;
        $this->refreshPreview();
    }

    public function toggleDepartment(string $department): void
    {
        if (in_array($department, $this->selectedDepartments, true)) {
            $this->selectedDepartments = array_values(array_filter(
                $this->selectedDepartments,
                fn (string $selectedDepartment): bool => $selectedDepartment !== $department,
            ));
        } else {
            $this->selectedDepartments[] = $department;
            $this->selectedDepartments = array_values(array_unique($this->selectedDepartments));
        }

        $this->showSuccess = false;
        $this->refreshPreview();
    }

    public function toggleUser(int $userId): void
    {
        if (in_array($userId, $this->selectedUserIds, true)) {
            $this->selectedUserIds = array_values(array_filter(
                $this->selectedUserIds,
                fn (int $selectedUserId): bool => $selectedUserId !== $userId,
            ));
        } else {
            $this->selectedUserIds[] = $userId;
            $this->selectedUserIds = array_values(array_unique($this->selectedUserIds));
        }

        $this->showSuccess = false;
        $this->refreshPreview();
    }

    public function createBatch(): void
    {
        $validated = $this->validate($this->rules(), $this->messages());

        $batchId = (string) Str::ulid();
        $deadline = now()->addDays((int) $validated['deadlineDays'])->timestamp;
        $enrolledBy = Auth::id();
        $createdCount = 0;
        $skippedCount = 0;

        foreach ($this->resolvedUsers() as $user) {
            $enrollment = Enrollment::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => (int) $validated['courseId'],
                ],
                [
                    'batch_id' => $batchId,
                    'enrolled_by' => $enrolledBy,
                    'deadline' => $deadline,
                    'enrolled_at' => now(),
                ],
            );

            if ($enrollment->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $skippedCount++;
            }
        }

        $this->createdBatchId = $batchId;
        $this->createdCount = $createdCount;
        $this->skippedCount = $skippedCount;
        $this->showSuccess = true;

        PostHogService::capture((string) Auth::id(), 'enrollment_batch_created', [
            'batch_id' => $batchId,
            'course_id' => (int) $validated['courseId'],
            'enrolled_count' => $createdCount,
            'skipped_count' => $skippedCount,
            'target_mode' => $this->targetMode,
        ]);

        $this->refreshPreview();
    }

    public function render(): View
    {
        $selectedCourse = $this->selectedCourse();

        return view('livewire.admin.enrollments.create', [
            'collegeOptions' => College::cases(),
            'courseOptions' => Course::query()
                ->when($this->courseSearch !== '', function (Builder $query): void {
                    $query->where('title', 'like', '%'.$this->courseSearch.'%');
                })
                ->orderBy('title')
                ->limit(8)
                ->get(),
            'departmentOptions' => Department::cases(),
            'selectedCourse' => $selectedCourse,
            'userOptions' => $this->userOptions(),
        ])->layout('layouts.app');
    }

    protected function rules(): array
    {
        return [
            'courseId' => ['required', 'integer', 'exists:courses,id'],
            'targetMode' => ['required', Rule::in(['all', 'college', 'user'])],
            'selectedColleges' => [
                Rule::requiredIf(fn (): bool => $this->targetMode === 'college'),
                'array',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($this->targetMode === 'college' && count($value) === 0) {
                        $fail('Choose at least one college for this enrollment batch.');
                    }
                },
            ],
            'selectedColleges.*' => [
                'string',
                Rule::in(array_map(static fn (College $college): string => $college->value, College::cases())),
            ],
            'selectedDepartments' => [
                'array',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($this->targetMode === 'departments' && count($value) === 0) {
                        $fail('Choose at least one department.');
                    }
                },
            ],
            'selectedDepartments.*' => [
                'string',
                Rule::in(array_map(static fn (Department $department): string => $department->value, Department::cases())),
            ],
            'selectedUserIds' => [
                Rule::requiredIf(fn (): bool => $this->targetMode === 'user'),
                'array',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($this->targetMode === 'user' && count($value) === 0) {
                        $fail('Choose at least one learner for this enrollment batch.');
                    }
                },
            ],
            'selectedUserIds.*' => [
                'integer',
                Rule::exists('users', 'id')->where(function ($query): void {
                    $query->whereIn('role', [Role::Staff->value, Role::Faculty->value]);
                }),
            ],
            'deadlineDays' => ['required', 'integer', 'min:1'],
        ];
    }

    protected function messages(): array
    {
        return [
            'courseId.required' => 'Choose a course before creating the batch.',
            'selectedColleges.required' => 'Choose at least one college for this enrollment batch.',
            'selectedDepartments.required' => 'Choose at least one department.',
            'selectedUserIds.required' => 'Choose at least one learner for this enrollment batch.',
        ];
    }

    protected function refreshPreview(): void
    {
        if (! $this->courseId) {
            $this->previewUsers = [];
            $this->resolvedUserCount = 0;
            $this->skippedDuplicateCount = 0;

            return;
        }

        $matchedUsers = $this->resolvedUsers();
        $enrolledUserIds = Enrollment::query()
            ->where('course_id', $this->courseId)
            ->pluck('user_id')
            ->all();

        $eligibleUsers = $matchedUsers
            ->reject(fn (User $user): bool => in_array($user->id, $enrolledUserIds, true))
            ->values();

        $this->resolvedUserCount = $eligibleUsers->count();
        $this->skippedDuplicateCount = $matchedUsers->count() - $eligibleUsers->count();
        $this->previewUsers = $eligibleUsers
            ->take(10)
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'college' => $user->college?->label() ?? 'N/A',
                'department' => $user->department?->label() ?? 'N/A',
            ])
            ->all();
    }

    protected function resolvedUsers(): Collection
    {
        return $this->resolvedUsersQuery()
            ->orderBy('name')
            ->get();
    }

    protected function resolvedUsersQuery(): Builder
    {
        $query = User::query()
            ->whereIn('role', [Role::Staff->value, Role::Faculty->value]);

        return match ($this->targetMode) {
            'college' => $query->when($this->selectedColleges !== [], function (Builder $builder): void {
                $builder->whereIn('college', $this->selectedColleges);

                if ($this->selectedDepartments !== []) {
                    $builder->whereIn('department', $this->selectedDepartments);
                }
            }, function (Builder $builder): void {
                $builder->whereRaw('1 = 0');
            }),
            'user' => $query->when($this->selectedUserIds !== [], function (Builder $builder): void {
                $builder->whereIn('id', $this->selectedUserIds);
            }, function (Builder $builder): void {
                $builder->whereRaw('1 = 0');
            }),
            default => $query,
        };
    }

    protected function userOptions(): Collection
    {
        $query = User::query()
            ->whereIn('role', [Role::Staff->value, Role::Faculty->value])
            ->when($this->userSearch !== '', function (Builder $builder): void {
                $builder->where(function (Builder $searchQuery): void {
                    $searchQuery
                        ->where('name', 'like', '%'.$this->userSearch.'%')
                        ->orWhere('email', 'like', '%'.$this->userSearch.'%');
                });
            });

        if ($this->courseId) {
            $query->whereDoesntHave('enrollments', function (Builder $builder): void {
                $builder->where('course_id', $this->courseId);
            });
        }

        return $query
            ->orderBy('name')
            ->limit(8)
            ->get();
    }

    protected function selectedCourse(): ?Course
    {
        return $this->courseId
            ? Course::query()->find($this->courseId)
            : null;
    }

    protected function syncCourseSelectionFromSlug(): void
    {
        $course = filled($this->courseSlug)
            ? Course::query()->where('slug', $this->courseSlug)->first()
            : null;

        $this->courseId = $course?->id;
        $this->courseSlug = $course?->slug;
    }
}
