<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Content;
use App\Models\Course;
use App\Models\Module;
use App\Models\Progress;
use App\Models\Topic;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public Course $course;

    public int $totalEnrollments = 0;

    public int $completedEnrollments = 0;

    public int $avgProgress = 0;

    public int $totalContent = 0;

    public ?string $quickEditType = null;

    public ?int $quickEditId = null;

    public string $quickEditTitle = '';

    public string $quickEditDescription = '';

    public string $quickEditUrl = '';

    public function mount(Course $course): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $this->course = $this->loadCourse($course->id);
        $this->calculateStats();
    }

    protected function loadCourse(int $courseId): Course
    {
        return Course::with(
            'courseMeta',
            'media',
            'topics.modules.contents.quiz.questions',
            'topics.modules.contents.endQuiz.questions',
            'topics.modules.contents.timestampedQuizzes.questions',
            'enrollments'
        )->findOrFail($courseId);
    }

    protected function calculateStats(): void
    {
        $this->totalEnrollments = $this->course->enrollments()->count();
        $contents = $this->getContentsThroughTopics();
        $this->totalContent = $contents->count();

        if ($this->totalEnrollments > 0 && $this->totalContent > 0) {
            $totalProgress = 0;
            $completed = 0;

            foreach ($this->course->enrollments as $enrollment) {
                $completedContent = Progress::where('user_id', $enrollment->user_id)
                    ->whereIn('content_id', $contents->pluck('id'))
                    ->whereNotNull('completed_at')
                    ->count();

                $progress = (int) round(($completedContent / $this->totalContent) * 100);
                $totalProgress += $progress;

                if ($progress === 100) {
                    $completed++;
                }
            }

            $this->avgProgress = (int) round($totalProgress / $this->totalEnrollments);
            $this->completedEnrollments = $completed;
        }
    }

    protected function getContentsThroughTopics()
    {
        $topicIds = $this->course->topics->pluck('id');

        return Content::whereIn('module_id', function ($query) use ($topicIds) {
            $query->select('id')
                ->from('modules')
                ->whereIn('topic_id', $topicIds);
        })->get();
    }

    public function startQuickEdit(string $type, int $id): void
    {
        $this->quickEditType = $type;
        $this->quickEditId = $id;
        $this->quickEditUrl = '';

        if ($type === 'topic') {
            $topic = Topic::findOrFail($id);
            $this->quickEditTitle = $topic->name;
            $this->quickEditDescription = $topic->description ?? '';

            return;
        }

        if ($type === 'module') {
            $module = Module::findOrFail($id);
            $this->quickEditTitle = $module->title;
            $this->quickEditDescription = $module->description ?? '';

            return;
        }

        $content = Content::findOrFail($id);
        $this->quickEditTitle = $content->title;
        $this->quickEditDescription = $content->body ?? '';
        $this->quickEditUrl = $content->content_url ?? '';
    }

    public function cancelQuickEdit(): void
    {
        $this->reset('quickEditType', 'quickEditId', 'quickEditTitle', 'quickEditDescription', 'quickEditUrl');
    }

    public function saveQuickEdit(): void
    {
        $this->validate($this->quickEditRules());

        if ($this->quickEditType === 'topic') {
            Topic::findOrFail($this->quickEditId)->update([
                'name' => $this->quickEditTitle,
                'description' => $this->quickEditDescription,
            ]);
        } elseif ($this->quickEditType === 'module') {
            Module::findOrFail($this->quickEditId)->update([
                'title' => $this->quickEditTitle,
                'description' => $this->quickEditDescription,
            ]);
        } else {
            Content::findOrFail($this->quickEditId)->update([
                'title' => $this->quickEditTitle,
                'body' => $this->quickEditDescription,
                'content_url' => $this->quickEditUrl,
            ]);
        }

        $this->refreshCourse();
        $this->cancelQuickEdit();
        session()->flash('success', 'Course details updated.');
    }

    protected function quickEditRules(): array
    {
        $rules = [
            'quickEditType' => 'required|in:topic,module,content',
            'quickEditId' => 'required|integer',
            'quickEditTitle' => 'required|string|max:255',
            'quickEditDescription' => 'nullable|string',
        ];

        if ($this->quickEditType === 'content') {
            $rules['quickEditUrl'] = 'nullable|url|max:2048';
        }

        return $rules;
    }

    protected function refreshCourse(): void
    {
        $this->course = $this->loadCourse($this->course->id);
        $this->calculateStats();
    }

    public function render(): View
    {
        return view('livewire.admin.courses.show');
    }
}
