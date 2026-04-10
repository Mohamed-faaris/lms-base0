<?php

use App\Livewire\Admin\Courses\Create as CreateCourse;
use App\Livewire\Admin\Courses\Edit as EditCourse;
use App\Models\Course;
use App\Models\CourseMeta;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('admin can create a course with metadata from the redesigned form', function () {
    $admin = User::factory()->admin()->create();

    Storage::fake('public');
    config()->set('media-library.disk_name', 'public');

    Livewire::actingAs($admin)
        ->test(CreateCourse::class)
        ->set('title', 'Instructional Design Essentials')
        ->set('slug', 'instructional-design-essentials')
        ->set('description', 'Build confident, outcome-driven learning experiences.')
        ->set('category', 'Teaching Practice')
        ->set('difficulty', 'Intermediate')
        ->set('duration', '4 modules • 3 hours')
        ->set('thumbnailUpload', UploadedFile::fake()->image('course-thumbnail.png'))
        ->set('audience', "New faculty\nProgram coordinators")
        ->set('outcomes', "Map outcomes to activities\nMeasure engagement clearly")
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.courses.show', 1));

    $course = Course::query()
        ->where('slug', 'instructional-design-essentials')
        ->first();

    expect($course)->not->toBeNull();
    expect($course?->title)->toBe('Instructional Design Essentials');
    expect($course?->description)->toBe('Build confident, outcome-driven learning experiences.');
    expect($course?->courseMeta)->not->toBeNull();
    expect($course?->courseMeta?->category)->toBe('Teaching Practice');
    expect($course?->courseMeta?->difficulty)->toBe('Intermediate');
    expect($course?->courseMeta?->duration)->toBe('4 modules • 3 hours');
    expect($course?->courseMeta?->data)->toMatchArray([
        'audience' => ['New faculty', 'Program coordinators'],
        'outcomes' => ['Map outcomes to activities', 'Measure engagement clearly'],
    ]);
    expect($course?->getFirstMedia('course-thumbnail'))->not->toBeNull();

    $media = $course?->getFirstMedia('course-thumbnail');

    expect($media)->not->toBeNull();
    Storage::disk('public')->assertExists($media->getPathRelativeToRoot());
});

test('admin can update existing course metadata from the edit form', function () {
    $admin = User::factory()->admin()->create();
    Storage::fake('public');
    config()->set('media-library.disk_name', 'public');

    $course = Course::create([
        'title' => 'Legacy Course',
        'slug' => 'legacy-course',
        'description' => 'Original description.',
    ]);

    CourseMeta::create([
        'course_id' => $course->id,
        'category' => 'Legacy',
        'difficulty' => 'Beginner',
        'duration' => '1 hour',
        'data' => [
            'audience' => ['Existing audience'],
            'outcomes' => ['Existing outcome'],
        ],
    ]);

    $course
        ->addMedia(UploadedFile::fake()->image('legacy-thumbnail.png'))
        ->usingName('legacy-thumbnail')
        ->usingFileName('legacy-thumbnail.png')
        ->toMediaCollection('course-thumbnail');

    Livewire::actingAs($admin)
        ->test(EditCourse::class, ['course' => $course])
        ->set('title', 'Modern Course')
        ->set('slug', 'modern-course')
        ->set('description', 'Updated description.')
        ->set('category', 'Leadership')
        ->set('difficulty', 'Advanced')
        ->set('duration', '6 modules • 5 hours')
        ->set('thumbnailUpload', UploadedFile::fake()->image('modern-thumbnail.png'))
        ->set('audience', "Department heads\nFaculty mentors")
        ->set('outcomes', "Coach faculty better\nImprove delivery quality")
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.courses.show', $course->id));

    $course->refresh();
    $course->load('courseMeta');

    expect($course->title)->toBe('Modern Course');
    expect($course->slug)->toBe('modern-course');
    expect($course->description)->toBe('Updated description.');
    expect($course->courseMeta)->not->toBeNull();
    expect($course->courseMeta->category)->toBe('Leadership');
    expect($course->courseMeta->difficulty)->toBe('Advanced');
    expect($course->courseMeta->duration)->toBe('6 modules • 5 hours');
    expect($course->courseMeta->data)->toMatchArray([
        'audience' => ['Department heads', 'Faculty mentors'],
        'outcomes' => ['Coach faculty better', 'Improve delivery quality'],
    ]);
    expect($course->getMedia('course-thumbnail'))->toHaveCount(1);

    $media = $course->getFirstMedia('course-thumbnail');

    expect($media)->not->toBeNull();
    Storage::disk('public')->assertExists($media->getPathRelativeToRoot());
});
