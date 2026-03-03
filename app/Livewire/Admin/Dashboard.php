<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;

class Dashboard extends Component
{
    public $totalColleges;
    public $totalDepartments;
    public $totalFaculties;
    public $totalCourses;
    public $completionRate;
    public $avgQuizScore;

    public function mount()
    {
        $this->totalColleges = 3;
        $this->totalDepartments = 12;
        $this->totalFaculties = User::where('role','faculty')->count();
        $this->totalCourses = Course::count();

        $this->completionRate = 78.5; // example (you can calculate later)
        $this->avgQuizScore = 72.4;   // example
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}