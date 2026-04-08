<?php

namespace App\Livewire\Admin\Users;

use App\Concerns\NormalizesEnrollmentDeadline;
use App\Models\Progress;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Profile extends Component
{
    use NormalizesEnrollmentDeadline;

    public User $user;

    public string $name = '';

    public string $email = '';

    public string $college = '';

    public string $department = '';

    public string $role = '';

    public bool $showEditModal = false;

    public bool $showResetPasswordModal = false;

    public string $newPassword = '';

    public string $confirmPassword = '';

    public function mount(User $user): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->college = $user->college?->label() ?? 'N/A';
        $this->department = $user->department?->label() ?? 'N/A';
        $this->role = $user->role?->label() ?? 'N/A';
    }

    public function openEditModal(): void
    {
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->reset('name', 'email');
    }

    public function saveProfile(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$this->user->id,
        ]);

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $this->closeEditModal();
        session()->flash('success', 'User profile updated successfully.');
    }

    public function openResetPasswordModal(): void
    {
        $this->showResetPasswordModal = true;
    }

    public function closeResetPasswordModal(): void
    {
        $this->showResetPasswordModal = false;
        $this->reset('newPassword', 'confirmPassword');
    }

    public function resetPassword(): void
    {
        $this->validate([
            'newPassword' => 'required|string|min:8',
            'confirmPassword' => 'required|same:newPassword',
        ]);

        $this->user->update([
            'password' => Hash::make($this->newPassword),
        ]);

        $this->closeResetPasswordModal();
        session()->flash('success', 'Password reset successfully.');
    }

    public function render(): View
    {
        $enrollments = $this->user->enrollments()->with(['course.topics.modules.contents'])->get();
        $enrollmentData = [];

        foreach ($enrollments as $enrollment) {
            $contents = $enrollment->course->topics->flatMap->modules->flatMap->contents;
            $totalContent = $contents->count();

            $completedContent = Progress::where('user_id', $this->user->id)
                ->whereIn('content_id', $contents->pluck('id'))
                ->whereNotNull('completed_at')
                ->count();

            $progress = $totalContent > 0 ? (int) round(($completedContent / $totalContent) * 100) : 0;
            $deadlineMeta = $this->normalizeEnrollmentDeadline((int) $enrollment->deadline);

            $enrollmentData[] = (object) [
                'course' => $enrollment->course->title,
                'courseUrl' => route('admin.courses.show', $enrollment->course->id),
                'enrolledAt' => $enrollment->enrolled_at?->format('M d, Y') ?? 'Unknown',
                'progress' => $progress,
                'deadlineLabel' => $deadlineMeta['label'],
                'deadlineTone' => match (true) {
                    $deadlineMeta['isOverdue'] => 'text-red-600 dark:text-red-400',
                    $deadlineMeta['isUrgent'] => 'text-amber-600 dark:text-amber-400',
                    default => 'text-zinc-600 dark:text-zinc-300',
                },
            ];
        }

        return view('livewire.admin.users.profile', [
            'enrollments' => $enrollmentData,
        ])->layout('layouts.app');
    }
}
