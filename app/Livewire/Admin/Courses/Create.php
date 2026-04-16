<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use App\Services\PostHogService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $slug = '';

    public string $description = '';

    public string $category = '';

    public string $difficulty = '';

    public string $duration = '';

    public string $audience = '';

    public string $outcomes = '';

    public string $requirements = '';

    public string $tags = '';

    public string $instructor = '';

    public bool $featured = false;

    public bool $published = false;

    public $thumbnailUpload = null;

    public function mount(): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }
    }

    public function updatedTitle(string $value): void
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:courses,slug',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'difficulty' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'thumbnailUpload' => 'nullable|image|max:10240',
            'audience' => 'nullable|string|max:2000',
            'outcomes' => 'nullable|string|max:2000',
            'requirements' => 'nullable|string|max:2000',
            'tags' => 'nullable|string|max:500',
            'instructor' => 'nullable|string|max:255',
            'featured' => 'nullable|boolean',
            'published' => 'nullable|boolean',
        ]);

        $course = Course::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?: Str::slug($validated['title']),
            'description' => $validated['description'],
        ]);

        $course->courseMeta()->create($this->courseMetaPayload());

        if ($this->thumbnailUpload !== null) {
            $course
                ->addMedia($this->thumbnailUpload->getRealPath())
                ->usingName(pathinfo($this->thumbnailUpload->getClientOriginalName(), PATHINFO_FILENAME))
                ->usingFileName(Str::uuid().'.'.$this->thumbnailUpload->getClientOriginalExtension())
                ->toMediaCollection('course-thumbnail');
        }

        PostHogService::capture((string) auth()->id(), 'course_created', [
            'course_id' => $course->id,
            'course_title' => $course->title,
            'course_slug' => $course->slug,
            'category' => $this->category ?: null,
            'difficulty' => $this->difficulty ?: null,
            'published' => $this->published,
        ]);

        session()->flash('success', 'Course created successfully.');

        $this->redirectRoute('admin.courses.show', $course->id);
    }

    public function render(): View
    {
        return view('livewire.admin.courses.create', [
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
                'requirements' => $this->normalizeLines($this->requirements),
                'tags' => $this->normalizeTags($this->tags),
                'instructor' => $this->instructor ?: null,
                'featured' => $this->featured,
                'published' => $this->published,
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

    protected function normalizeTags(string $value): array
    {
        if (empty(trim($value))) {
            return [];
        }

        return array_values(array_filter(
            array_map(static fn (string $tag): string => trim($tag), explode(',', $value)),
            static fn (string $tag): bool => $tag !== '',
        ));
    }
}
