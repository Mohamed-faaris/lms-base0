<?php

namespace App\Livewire\Faculty;

use App\Models\Certificate;
use App\Models\Progress;
use Livewire\Component;

class Certificates extends Component
{
    public array $completedCourses = [];
    public array $progressHistory = [];
    
    public ?array $selectedCourse = null;
    public string $recipientName = '';
    public bool $isEditing = false;
    public string $activeTab = 'certificates';

    public function mount()
    {
        $user = auth()->user();
        $this->recipientName = $user->name ?? '';
        $this->loadCertificates();
        $this->loadProgressHistory();
    }

    protected function loadCertificates(): void
    {
        $user = auth()->user();
        
        $certificates = Certificate::with('course')
            ->where('user_id', $user->id)
            ->orderBy('completed_at', 'desc')
            ->get();

        $this->completedCourses = $certificates->map(function ($cert) {
            $course = $cert->course;
            $contents = $course?->contents ?? collect();
            
            return [
                'id' => $cert->course_id,
                'name' => $course?->title ?? 'Unknown Course',
                'completedDate' => $cert->completed_at->format('M d, Y'),
                'score' => 100,
                'duration' => $this->calculateDuration($contents->count()),
                'certificateId' => $cert->certificate_id,
                'hasCertificate' => true,
            ];
        })->toArray();
    }

    protected function loadProgressHistory(): void
    {
        $user = auth()->user();
        
        $progressRecords = Progress::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->with('content.course')
            ->orderBy('completed_at', 'desc')
            ->limit(20)
            ->get();

        $this->progressHistory = $progressRecords->map(function ($progress) {
            return [
                'date' => $progress->completed_at->format('M d, Y'),
                'action' => 'Completed Module',
                'course' => $progress->content?->course?->title ?? 'Unknown',
                'xp' => 100,
            ];
        })->toArray();
    }

    protected function calculateDuration(int $moduleCount): string
    {
        $hours = max(1, (int) ceil($moduleCount / 2));
        return $hours . ' hour' . ($hours > 1 ? 's' : '');
    }

    public function viewCertificate($courseId)
    {
        $this->selectedCourse = collect($this->completedCourses)->firstWhere('id', $courseId);
        $this->recipientName = auth()->user()->name ?? '';
        $this->isEditing = false;
    }

    public function closeCertificate()
    {
        $this->selectedCourse = null;
    }

    public function toggleEditName()
    {
        $this->isEditing = !$this->isEditing;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.faculty.certificates')->layout('layouts.app');
    }
}
