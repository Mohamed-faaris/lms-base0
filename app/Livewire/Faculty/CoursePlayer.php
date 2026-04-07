<?php

namespace App\Livewire\Faculty;

use App\Models\Content;
use App\Models\Course;
use App\Models\Progress;
use Illuminate\Support\Collection;
use Livewire\Component;

class CoursePlayer extends Component
{
    public Course $course;

    public Content $currentModule;

    public Collection $modules;

    public int $totalModules = 0;

    public int $completedModules = 0;

    public int $courseProgress = 0;

    public bool $sidebarOpen = true;

    public bool $mobileDrawerOpen = false;

    public bool $showQuiz = false;

    public bool $quizSubmitted = false;

    public ?int $quizScore = null;

    public array $quizAnswers = [];

    public bool $showFeedback = false;

    public bool $showPuzzle = false;

    public array $quizQuestions = [
        [
            'id' => 'q1',
            'question' => 'Which of the following best describes active learning?',
            'options' => [
                ['id' => 'a', 'text' => 'Listening to lectures passively'],
                ['id' => 'b', 'text' => 'Engaging students through activities and discussions'],
                ['id' => 'c', 'text' => 'Reading textbooks only'],
                ['id' => 'd', 'text' => 'Watching videos without interaction'],
            ],
            'correctAnswer' => 'b',
        ],
        [
            'id' => 'q2',
            'question' => 'What is the primary benefit of formative assessment?',
            'options' => [
                ['id' => 'a', 'text' => 'Grading students at the end of a course'],
                ['id' => 'b', 'text' => 'Providing ongoing feedback for improvement'],
                ['id' => 'c', 'text' => 'Replacing final exams'],
                ['id' => 'd', 'text' => 'Reducing teaching workload'],
            ],
            'correctAnswer' => 'b',
        ],
        [
            'id' => 'q3',
            'question' => 'Which strategy promotes higher-order thinking?',
            'options' => [
                ['id' => 'a', 'text' => 'Memorization drills'],
                ['id' => 'b', 'text' => 'Multiple choice tests only'],
                ['id' => 'c', 'text' => 'Problem-based learning'],
                ['id' => 'd', 'text' => 'Lecture-only format'],
            ],
            'correctAnswer' => 'c',
        ],
    ];

    public function mount(?Course $course = null): void
    {
        $user = auth()->user();

        if (! $course || ! $course->exists) {
            $this->course = Course::whereHas('enrollments', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->firstOrFail();
        } else {
            $this->course = $course;
        }

        $this->loadCourseData();
    }

    protected function loadCourseData(): void
    {
        $user = auth()->user();

        $this->course->loadMissing('topics.modules.contents');

        $this->modules = $this->course->topics
            ->sortBy('order')
            ->flatMap(fn ($topic) => $topic->modules->sortBy('order'))
            ->flatMap(fn ($module) => $module->contents->sortBy('order'))
            ->values();

        $this->modules = Content::query()
            ->whereIn('id', $this->modules->pluck('id'))
            ->orderBy('id')
            ->get();

        $this->totalModules = $this->modules->count();

        if ($this->totalModules === 0) {
            return;
        }

        $completedContentIds = Progress::where('user_id', $user->id)
            ->whereIn('content_id', $this->modules->pluck('id'))
            ->whereNotNull('completed_at')
            ->pluck('content_id')
            ->toArray();

        $this->completedModules = count($completedContentIds);
        $this->courseProgress = (int) round(($this->completedModules / $this->totalModules) * 100);

        // Find current module (first incomplete, or last if all complete)
        $firstIncomplete = $this->modules->first(fn ($module) => ! in_array($module->id, $completedContentIds));
        $this->currentModule = $firstIncomplete ?? $this->modules->last();

        // Format modules for UI
        $this->modules->transform(function ($module, $key) use ($completedContentIds) {
            $status = in_array($module->id, $completedContentIds) ? 'completed' : 'locked';

            // Allow clicking if it's completed, or if it's the current one being worked on
            if ($module->id === $this->currentModule->id) {
                $status = 'in-progress';
            }

            // Allow sequential unlocking
            $previousCompleted = $key === 0 || in_array($this->modules[$key - 1]->id, $completedContentIds);
            if ($status === 'locked' && $previousCompleted) {
                $status = 'unlocked'; // Or treat as in-progress for accessibility
            }

            $module->status = $status;
            // Mock data for UI
            $module->duration = '15:00';
            $module->videoId = 'dQw4w9WgXcQ';

            return $module;
        });
    }

    public function selectModule(int $moduleId): void
    {
        $module = $this->modules->firstWhere('id', $moduleId);
        if ($module && $module->status !== 'locked') {
            $this->currentModule = $module;
            $this->resetQuiz();
            $this->mobileDrawerOpen = false;
        }
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    public function toggleMobileDrawer(): void
    {
        $this->mobileDrawerOpen = ! $this->mobileDrawerOpen;
    }

    public function startQuiz(): void
    {
        $this->showQuiz = true;
    }

    public function resetQuiz(): void
    {
        $this->showQuiz = false;
        $this->quizSubmitted = false;
        $this->quizScore = null;
        $this->quizAnswers = [];
    }

    public function setAnswer(string $questionId, string $answerId): void
    {
        $this->quizAnswers[$questionId] = $answerId;
    }

    public function submitQuiz(): void
    {
        if (count($this->quizAnswers) < count($this->quizQuestions)) {
            return;
        }

        $correct = 0;
        foreach ($this->quizQuestions as $q) {
            if (isset($this->quizAnswers[$q['id']]) && $this->quizAnswers[$q['id']] === $q['correctAnswer']) {
                $correct++;
            }
        }
        $this->quizScore = (int) round(($correct / count($this->quizQuestions)) * 100);
        $this->quizSubmitted = true;

        if ($this->quizScore >= 80) {
            $this->markCurrentModuleComplete();
        }
    }

    public function markCurrentModuleComplete(): void
    {
        $user = auth()->user();
        Progress::updateOrCreate(
            ['user_id' => $user->id, 'content_id' => $this->currentModule->id],
            ['completed_at' => now()]
        );
        $this->loadCourseData();
    }

    public function retakeQuiz(): void
    {
        $this->quizSubmitted = false;
        $this->quizScore = null;
        $this->quizAnswers = [];
    }

    public function toggleFeedback(): void
    {
        $this->showFeedback = ! $this->showFeedback;
    }

    public function togglePuzzle(): void
    {
        $this->showPuzzle = ! $this->showPuzzle;
    }

    public function render()
    {
        return view('livewire.faculty.course-player')->layout('layouts.app');
    }
}
