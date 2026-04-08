<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Content;
use App\Models\Course;
use App\Models\Module;
use Livewire\Component;

class ContentEditor extends Component
{
    public ?Course $course = null;

    public ?Content $content = null;

    public int $contentId;

    public string $title = '';

    public string $body = '';

    public string $type = 'video';

    public string $contentUrl = '';

    public ?int $selectedModuleId = null;

    public array $availableModules = [];

    public function mount(?int $courseId = null, ?int $contentId = null): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($contentId) {
            $this->contentId = $contentId;
            $this->content = Content::with('module.topic.course')->findOrFail($contentId);
            $this->course = $this->content->module->topic->course;
            $this->loadContentData();
        } elseif ($courseId) {
            $this->course = Course::findOrFail($courseId);
            $this->loadAvailableModules();
        }
    }

    protected function loadAvailableModules(): void
    {
        $topicIds = $this->course->topics->pluck('id');
        $this->availableModules = Module::whereIn('topic_id', $topicIds)->get()->toArray();
    }

    protected function loadContentData(): void
    {
        if ($this->content) {
            $this->loadAvailableModules();
            $this->title = $this->content->title;
            $this->body = $this->content->body ?? '';
            $this->type = $this->content->type->value;
            $this->contentUrl = $this->content->content_url ?? '';
            $this->selectedModuleId = $this->content->module_id;
        }
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,article,quiz',
            'selectedModuleId' => 'required|exists:modules,id',
        ]);

        if ($this->content) {
            $this->content->update([
                'title' => $this->title,
                'body' => $this->body,
                'type' => $this->type,
                'content_url' => $this->contentUrl,
            ]);
            session()->flash('success', 'Content updated successfully.');
        } else {
            $content = Content::create([
                'module_id' => $this->selectedModuleId,
                'title' => $this->title,
                'body' => $this->body,
                'type' => $this->type,
                'content_url' => $this->contentUrl,
                'order' => $this->getNextContentOrder($this->selectedModuleId),
            ]);
            session()->flash('success', 'Content created successfully.');
            $this->redirectRoute('admin.courses.content.show', $content->id);

            return;
        }

        $this->redirectRoute('admin.courses.show', $this->course->id);
    }

    protected function getNextContentOrder(int $moduleId): int
    {
        return Content::where('module_id', $moduleId)->max('order') + 1;
    }

    public function render()
    {
        return view('livewire.admin.courses.content-editor');
    }
}
