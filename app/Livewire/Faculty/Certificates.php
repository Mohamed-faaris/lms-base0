<?php

namespace App\Livewire\Faculty;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\Progress;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Livewire\Component;

class Certificates extends Component
{
    public array $completedCourses = [];

    public array $progressHistory = [];

    public ?array $selectedCourse = null;

    public bool $showModal = false;

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
            $moduleCount = $contents->count();

            $enrollment = Enrollment::where('user_id', $cert->user_id)
                ->where('course_id', $cert->course_id)
                ->first();

            $instructor = null;
            if ($enrollment && $enrollment->enrolled_by) {
                $instructor = \App\Models\User::find($enrollment->enrolled_by);
            }

            return [
                'id' => $cert->course_id,
                'name' => $course?->title ?? 'Unknown Course',
                'description' => $course?->description ?? '',
                'completedDate' => $cert->completed_at->format('M d, Y'),
                'issueDate' => $cert->issued_at ? $cert->issued_at->format('M d, Y') : $cert->completed_at->format('M d, Y'),
                'score' => 100,
                'duration' => $this->calculateDuration($moduleCount),
                'durationHours' => max(1, (int) ceil($moduleCount / 2)),
                'certificateId' => $cert->certificate_id,
                'hasCertificate' => true,
                'instructorName' => $instructor?->name ?? 'System',
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

        return $hours.' hour'.($hours > 1 ? 's' : '');
    }

    public function viewCertificate($courseId)
    {
        $this->selectedCourse = collect($this->completedCourses)->firstWhere('id', $courseId);
        $this->showModal = true;
        $this->recipientName = auth()->user()->name ?? '';
        $this->isEditing = false;
    }

    public function closeCertificate()
    {
        $this->showModal = false;
        $this->selectedCourse = null;
    }

    public function toggleEditName()
    {
        $this->isEditing = ! $this->isEditing;
    }

    public function generateQrCode(string $certificateId): string
    {
        $verificationUrl = route('certificates.verify', ['certificate_id' => $certificateId]);

        $qrCode = QrCode::create($verificationUrl)
            ->setSize(150)
            ->setMargin(5)
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Medium);

        $writer = new SvgWriter;
        $result = $writer->write($qrCode);

        return $result->getString();
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
