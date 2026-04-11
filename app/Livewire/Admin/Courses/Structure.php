<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Content;
use App\Models\Course;
use App\Models\Module;
use App\Models\Progress;
use App\Models\Topic;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Structure extends Component
{
    public Course $course;

    public int $totalEnrollments = 0;

    public int $completedEnrollments = 0;

    public int $avgProgress = 0;

    public int $totalContent = 0;

    public array $expandedTopics = [];

    public array $expandedModules = [];

    public bool $showTopicModal = false;

    public bool $showModuleModal = false;

    public bool $showContentModal = false;

    public ?Topic $editingTopic = null;

    public ?Module $editingModule = null;

    public ?Content $editingContent = null;

    public string $topicName = '';

    public string $topicDescription = '';

    public string $topicOrder = '';

    public string $moduleTitle = '';

    public string $moduleDescription = '';

    public string $moduleOrder = '';

    public ?int $selectedTopicId = null;

    public string $contentTitle = '';

    public string $contentBody = '';

    public string $contentType = 'video';

    public string $contentUrl = '';

    public ?int $selectedModuleId = null;

    public function mount(Course $course): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $this->course = $this->loadCourse($course->id);
        $this->calculateStats();

        foreach ($this->course->topics as $topic) {
            $this->expandedTopics[$topic->id] = true;

            foreach ($topic->modules as $module) {
                $this->expandedModules[$module->id] = true;
            }
        }
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

    public function toggleTopic(int $topicId): void
    {
        $this->expandedTopics[$topicId] = ! ($this->expandedTopics[$topicId] ?? false);
    }

    public function toggleModule(int $moduleId): void
    {
        $this->expandedModules[$moduleId] = ! ($this->expandedModules[$moduleId] ?? false);
    }

    public function openTopicModal($topic = null): void
    {
        if (is_numeric($topic)) {
            $topic = Topic::find($topic);
        }

        if ($topic instanceof Topic) {
            $this->editingTopic = $topic;
            $this->topicName = $topic->name;
            $this->topicDescription = $topic->description ?? '';
            $this->topicOrder = (string) $topic->order;
        } else {
            $this->editingTopic = null;
            $this->topicName = '';
            $this->topicDescription = '';
            $this->topicOrder = (string) ($this->course->topics->max('order') + 1);
        }

        $this->showTopicModal = true;
    }

    public function saveTopic(): void
    {
        $this->validate([
            'topicName' => 'required|string|max:255',
            'topicOrder' => 'required|integer|min:1',
        ]);

        if ($this->editingTopic) {
            $this->editingTopic->update([
                'name' => $this->topicName,
                'description' => $this->topicDescription,
                'order' => (int) $this->topicOrder,
            ]);
        } else {
            Topic::create([
                'course_id' => $this->course->id,
                'name' => $this->topicName,
                'description' => $this->topicDescription,
                'order' => (int) $this->topicOrder,
            ]);
        }

        $this->refreshCourse();
        $this->closeTopicModal();
    }

    public function closeTopicModal(): void
    {
        $this->showTopicModal = false;
        $this->editingTopic = null;
    }

    public function openModuleModal($module = null): void
    {
        if (is_numeric($module)) {
            $module = Module::find($module);
        }

        if ($module instanceof Module) {
            $this->editingModule = $module;
            $this->moduleTitle = $module->title;
            $this->moduleDescription = $module->description ?? '';
            $this->moduleOrder = (string) $module->order;
            $this->selectedTopicId = $module->topic_id;
        } else {
            $this->editingModule = null;
            $this->moduleTitle = '';
            $this->moduleDescription = '';
            $this->moduleOrder = '';
        }

        $this->showModuleModal = true;
    }

    public function saveModule(): void
    {
        $this->validate([
            'moduleTitle' => 'required|string|max:255',
            'selectedTopicId' => 'required|exists:topics,id',
        ]);

        if (empty($this->moduleOrder)) {
            $topic = Topic::find($this->selectedTopicId);
            $this->moduleOrder = (string) (($topic?->modules->max('order') ?? 0) + 1);
        }

        if ($this->editingModule) {
            $this->editingModule->update([
                'title' => $this->moduleTitle,
                'description' => $this->moduleDescription,
                'order' => (int) $this->moduleOrder,
                'topic_id' => $this->selectedTopicId,
            ]);
        } else {
            Module::create([
                'topic_id' => $this->selectedTopicId,
                'title' => $this->moduleTitle,
                'description' => $this->moduleDescription,
                'order' => (int) $this->moduleOrder,
            ]);
        }

        $this->refreshCourse();
        $this->closeModuleModal();
    }

    public function closeModuleModal(): void
    {
        $this->showModuleModal = false;
        $this->editingModule = null;
    }

    public function openContentModal($content = null): void
    {
        if ($content instanceof Content) {
            $this->editingContent = $content;
        } elseif (is_numeric($content)) {
            $this->editingContent = Content::find($content);
        } else {
            $this->editingContent = null;
        }

        if ($this->editingContent) {
            $this->contentTitle = $this->editingContent->title;
            $this->contentBody = $this->editingContent->body ?? '';
            $this->contentType = $this->editingContent->type->value;
            $this->contentUrl = $this->editingContent->content_url ?? '';
            $this->selectedModuleId = $this->editingContent->module_id;
        } else {
            $this->contentTitle = '';
            $this->contentBody = '';
            $this->contentType = 'video';
            $this->contentUrl = '';
        }

        $this->showContentModal = true;
    }

    public function saveContent(): void
    {
        $this->validate([
            'contentTitle' => 'required|string|max:255',
            'contentType' => 'required|in:video,article,ppt,quiz',
            'selectedModuleId' => 'required|exists:modules,id',
        ]);

        if ($this->editingContent) {
            $this->editingContent->update([
                'title' => $this->contentTitle,
                'body' => $this->contentBody,
                'type' => $this->contentType,
                'content_url' => $this->contentUrl,
            ]);
        } else {
            Content::create([
                'module_id' => $this->selectedModuleId,
                'title' => $this->contentTitle,
                'body' => $this->contentBody,
                'type' => $this->contentType,
                'content_url' => $this->contentUrl,
                'order' => $this->getNextContentOrder($this->selectedModuleId),
            ]);
        }

        $this->refreshCourse();
        $this->closeContentModal();
    }

    protected function getNextContentOrder(int $moduleId): int
    {
        return (int) Content::where('module_id', $moduleId)->max('order') + 1;
    }

    public function closeContentModal(): void
    {
        $this->showContentModal = false;
        $this->editingContent = null;
    }

    public function deleteContent(int $contentId): void
    {
        Content::findOrFail($contentId)->delete();
        $this->refreshCourse();
    }

    protected function refreshCourse(): void
    {
        $this->course = $this->loadCourse($this->course->id);
        $this->calculateStats();
    }

    public function render(): View
    {
        return view('livewire.admin.courses.structure');
    }
}
