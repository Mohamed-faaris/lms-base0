<?php

use App\Enums\ContentType;
use App\Livewire\Admin\Courses\Show as ShowCourse;
use App\Livewire\Admin\Courses\Structure as StructureCourse;
use App\Models\Content;
use App\Models\Course;
use App\Models\Module;
use App\Models\Topic;
use App\Models\User;
use Livewire\Livewire;

test('admin can open the new course structure page', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Structure Course',
        'slug' => 'structure-course',
        'description' => 'Course for structure page.',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.courses.structure', $course));

    $response->assertSuccessful();
    $response->assertSee('Course Structure');
    $response->assertSee('Authoring Workspace');
});

test('course show page renders all nested content in a read-first layout', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Viewer Course',
        'slug' => 'viewer-course',
        'description' => 'Course for show page.',
    ]);
    $topic = Topic::create([
        'course_id' => $course->id,
        'name' => 'Topic Alpha',
        'description' => 'Topic description.',
        'order' => 1,
    ]);
    $module = Module::create([
        'topic_id' => $topic->id,
        'title' => 'Module Alpha',
        'description' => 'Module description.',
        'order' => 1,
    ]);
    Content::create([
        'module_id' => $module->id,
        'order' => 1,
        'title' => 'Video Lesson',
        'body' => 'Video lesson body.',
        'type' => ContentType::Video,
        'content_url' => 'https://example.com/video',
    ]);
    Content::create([
        'module_id' => $module->id,
        'order' => 2,
        'title' => 'Article Lesson',
        'body' => 'Article lesson body.',
        'type' => ContentType::Article,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.courses.show', $course));

    $response->assertSuccessful();
    $response->assertSee('Course Viewer');
    $response->assertSee('Topic Alpha');
    $response->assertSee('Module Alpha');
    $response->assertSee('Video Lesson');
    $response->assertSee('Article Lesson');
});

test('admin can quick edit content from the course show page', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Quick Edit Course',
        'slug' => 'quick-edit-course',
        'description' => 'Course for quick edits.',
    ]);
    $topic = Topic::create([
        'course_id' => $course->id,
        'name' => 'Topic One',
        'description' => 'Original topic description.',
        'order' => 1,
    ]);
    $module = Module::create([
        'topic_id' => $topic->id,
        'title' => 'Module One',
        'description' => 'Original module description.',
        'order' => 1,
    ]);
    $content = Content::create([
        'module_id' => $module->id,
        'order' => 1,
        'title' => 'Original Lesson',
        'body' => 'Original body.',
        'type' => ContentType::Article,
        'content_url' => 'https://example.com/original',
    ]);

    Livewire::actingAs($admin)
        ->test(ShowCourse::class, ['course' => $course])
        ->call('startQuickEdit', 'content', $content->id)
        ->set('quickEditTitle', 'Updated Lesson')
        ->set('quickEditDescription', 'Updated body.')
        ->set('quickEditUrl', 'https://example.com/updated')
        ->call('saveQuickEdit')
        ->assertHasNoErrors();

    $content->refresh();

    expect($content->title)->toBe('Updated Lesson');
    expect($content->body)->toBe('Updated body.');
    expect($content->content_url)->toBe('https://example.com/updated');
});

test('structure page livewire component renders structure controls', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Livewire Structure Course',
        'slug' => 'livewire-structure-course',
        'description' => 'Course for structure livewire test.',
    ]);

    Livewire::actingAs($admin)
        ->test(StructureCourse::class, ['course' => $course])
        ->assertSee('Authoring Workspace')
        ->assertSee('Add Topic');
});
