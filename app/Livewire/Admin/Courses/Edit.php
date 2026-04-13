<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use App\Services\PostHogService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public Course $course;

    public string $title = '';

    public string $slug = '';

    public string $description = '';

    public string $category = '';

    public string $difficulty = '';

    public string $duration = '';

    public string $audience = '';

    public string $outcomes = '';

    public $thumbnailUpload = null;

    public function mount(Course $course): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $this->course = Course::with(
            'courseMeta',
            'topics.modules.contents.quiz',
            'topics.modules.contents.timestampedQuizzes',
            'topics.modules.contents.endQuiz',
            'enrollments',
            'media',
        )->findOrFail($course->id);
        $this->title = $this->course->title;
        $this->slug = $this->course->slug ?? '';
        $this->description = $this->course->description ?? '';
        $this->category = $this->course->courseMeta?->category ?? '';
        $this->difficulty = $this->course->courseMeta?->difficulty ?? '';
        $this->duration = $this->course->courseMeta?->duration ?? '';
        $this->audience = implode(PHP_EOL, $this->course->courseMeta?->data['audience'] ?? []);
        $this->outcomes = implode(PHP_EOL, $this->course->courseMeta?->data['outcomes'] ?? []);
    }

    public function updatedTitle(string $value): void
    {
        if (empty($this->slug) || $this->slug === Str::slug($this->course->title)) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:courses,slug,'.$this->course->id,
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'difficulty' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'thumbnailUpload' => 'nullable|image|max:10240',
            'audience' => 'nullable|string|max:2000',
            'outcomes' => 'nullable|string|max:2000',
        ]);

        $this->course->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?: Str::slug($validated['title']),
            'description' => $validated['description'],
        ]);

        if ($this->course->courseMeta !== null) {
            $this->course->courseMeta->update($this->courseMetaPayload());
        } else {
            $this->course->courseMeta()->create($this->courseMetaPayload());
        }

        if ($this->thumbnailUpload !== null) {
            $this->course
                ->addMedia($this->thumbnailUpload->getRealPath())
                ->usingName(pathinfo($this->thumbnailUpload->getClientOriginalName(), PATHINFO_FILENAME))
                ->usingFileName(Str::uuid().'.'.$this->thumbnailUpload->getClientOriginalExtension())
                ->toMediaCollection('course-thumbnail');
        }

        $this->course->load('courseMeta', 'media');

        PostHogService::capture((string) auth()->id(), 'course_updated', [
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'course_slug' => $this->course->slug,
            'category' => $this->category ?: null,
            'difficulty' => $this->difficulty ?: null,
        ]);

        session()->flash('success', 'Course updated successfully.');

        $this->redirectRoute('admin.courses.show', $this->course->id);
    }

    public function render(): View
    {
        $this->course->loadMissing(
            'topics.modules.contents.quiz',
            'topics.modules.contents.timestampedQuizzes',
            'topics.modules.contents.endQuiz',
            'enrollments',
            'media',
        );

        return view('livewire.admin.courses.edit', [
            'difficultyOptions' => ['Beginner', 'Intermediate', 'Advanced'],
        ]);
    }

    protected function courseMetaPayload(): array
    {
        return [
            'category' => filled($this->category) ? $this->category : null,
            'difficulty' => filled($this->difficulty) ? $this->difficulty : null,
            'duration' => filled($this->duration) ? $this->duration : null,
            'data' => [
                'audience' => $this->normalizeLines($this->audience),
                'outcomes' => $this->normalizeLines($this->outcomes),
            ],
        ];
    }

    protected function normalizeLines(string $value): array
    {
        return array_values(array_filter(
            array_map(static fn (string $line): string => trim($line), preg_split('/\r\n|\r|\n/', $value) ?: []),
            static fn (string $line): bool => $line !== '',
        ));
    }
}
