<?php

namespace App\Livewire\Faculty;

use Livewire\Component;

class Certificates extends Component
{
    public array $completedCourses = [];

    public array $completedCoursesIndex = [];

    public array $progressHistory = [];

    public bool $showCertificateModal = false;

    public ?array $selectedCourse = null;

    public string $recipientName = '';

    public string $activeTab = 'certificates';

    public function mount(): void
    {
        $user = auth()->user();
        $this->recipientName = $user->name ?? '';

        // Mock data matching the sample
        $rawCompletedCourses = [
            [
                'id' => 1,
                'name' => 'Teaching Methodologies',
                'completedDate' => 'Jan 15, 2026',
                'score' => 92,
                'duration' => '4 hours',
                'certificateId' => 'CERT-TM-2026-001',
                'hasCertificate' => true,
            ],
            [
                'id' => 2,
                'name' => 'Research Ethics',
                'completedDate' => 'Jan 10, 2026',
                'score' => 88,
                'duration' => '3 hours',
                'certificateId' => 'CERT-RE-2026-002',
                'hasCertificate' => true,
            ],
            [
                'id' => 3,
                'name' => 'Lab Safety Standards',
                'completedDate' => 'Dec 28, 2025',
                'score' => 95,
                'duration' => '2 hours',
                'certificateId' => 'CERT-LS-2025-003',
                'hasCertificate' => true,
            ],
        ];
        $this->completedCourses = $rawCompletedCourses;
        $this->completedCoursesIndex = [];

        foreach ($rawCompletedCourses as $course) {
            $this->completedCoursesIndex[$course['id']] = $course;
        }

        $this->progressHistory = [
            ['date' => 'Jan 29, 2026', 'action' => 'Completed Module 6', 'course' => 'Digital Pedagogy', 'xp' => 100],
            ['date' => 'Jan 28, 2026', 'action' => 'Passed Quiz', 'course' => 'Digital Pedagogy', 'xp' => 75],
            ['date' => 'Jan 27, 2026', 'action' => 'Started Course', 'course' => 'Assessment Design', 'xp' => 25],
            ['date' => 'Jan 25, 2026', 'action' => 'Earned Certificate', 'course' => 'Research Ethics', 'xp' => 200],
            ['date' => 'Jan 24, 2026', 'action' => 'Completed Module 5', 'course' => 'Research Ethics', 'xp' => 100],
            ['date' => 'Jan 22, 2026', 'action' => 'Passed Quiz', 'course' => 'Research Ethics', 'xp' => 75],
            ['date' => 'Jan 20, 2026', 'action' => 'Completed Module 4', 'course' => 'Research Ethics', 'xp' => 100],
            ['date' => 'Jan 15, 2026', 'action' => 'Earned Certificate', 'course' => 'Teaching Methodologies', 'xp' => 200],
        ];
    }

    public function viewCertificate(int $courseId): void
    {
        $this->selectedCourse = $this->completedCoursesIndex[$courseId] ?? null;
        $this->showCertificateModal = $this->selectedCourse !== null;
        $this->recipientName = auth()->user()->name ?? '';
    }

    public function downloadCertificate(int $courseId): void
    {
        $course = $this->completedCoursesIndex[$courseId] ?? null;

        if (! $course) {
            return;
        }

        $this->dispatchBrowserEvent('certificate-download', [
            'courseId' => $courseId,
            'name' => $course['name'],
            'certificateId' => $course['certificateId'],
        ]);
    }

    public function closeCertificate(): void
    {
        $this->showCertificateModal = false;
        $this->selectedCourse = null;
    }

    public function updatedShowCertificateModal(bool $isOpen): void
    {
        if (! $isOpen) {
            $this->selectedCourse = null;
        }
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.faculty.certificates')->layout('layouts.app');
    }
}
