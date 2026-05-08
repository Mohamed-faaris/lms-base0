<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\BehavioralAnalyticsService;
use Livewire\Component;

class BehavioralAnalytics extends Component
{
    public User $selectedFaculty;

    public ?int $selectedCourseId = null;

    public array $facultyData = [];

    public array $courseData = [];

    public int $avgEngagement = 0;

    public int $avgFocus = 0;

    public int $avgCompliance = 0;

    public int $avgConsistency = 0;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $admin = auth()->user();
        $service = app(BehavioralAnalyticsService::class);

        $this->facultyData = $service->getFacultyAnalyticsForAdmin($admin)->toArray();

        $this->calculateAverages();

        if ($this->selectedCourseId) {
            $this->courseData = $service->getCourseAnalytics($this->selectedCourseId);
        }
    }

    protected function calculateAverages(): void
    {
        if (empty($this->facultyData)) {
            return;
        }

        $count = count($this->facultyData);

        $engagementSum = array_sum(array_column($this->facultyData, 'engagement_score'));
        $focusSum = array_sum(array_column($this->facultyData, 'focus_score'));
        $complianceSum = array_sum(array_column($this->facultyData, 'compliance_score'));
        $consistencySum = array_sum(array_column($this->facultyData, 'consistency_score'));

        $this->avgEngagement = (int) round($engagementSum / $count);
        $this->avgFocus = (int) round($focusSum / $count);
        $this->avgCompliance = (int) round($complianceSum / $count);
        $this->avgConsistency = (int) round($consistencySum / $count);
    }

    public function render()
    {
        return view('livewire.admin.behavioral-analytics');
    }
}
