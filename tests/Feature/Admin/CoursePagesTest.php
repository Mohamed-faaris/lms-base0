<?php

use App\Enums\ContentType;
use App\Enums\QuizKind;
use App\Livewire\Admin\Courses\QuizEditor;
use App\Livewire\Admin\Courses\Show as ShowCourse;
use App\Livewire\Admin\Courses\Structure as StructureCourse;
use App\Models\Content;
use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
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

test('admin can move modules up and down within a topic from structure page', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Module Reorder Course',
        'slug' => 'module-reorder-course',
        'description' => 'Course for module reorder.',
    ]);
    $topic = Topic::create([
        'course_id' => $course->id,
        'name' => 'Topic One',
        'description' => 'Topic description.',
        'order' => 1,
    ]);
    $moduleOne = Module::create([
        'topic_id' => $topic->id,
        'title' => 'Module One',
        'description' => 'Module one description.',
        'order' => 1,
    ]);
    $moduleTwo = Module::create([
        'topic_id' => $topic->id,
        'title' => 'Module Two',
        'description' => 'Module two description.',
        'order' => 2,
    ]);

    Livewire::actingAs($admin)
        ->test(StructureCourse::class, ['course' => $course])
        ->call('moveModuleUp', $moduleTwo->id)
        ->assertHasNoErrors();

    expect($moduleOne->fresh()->order)->toBe(2);
    expect($moduleTwo->fresh()->order)->toBe(1);

    Livewire::actingAs($admin)
        ->test(StructureCourse::class, ['course' => $course])
        ->call('moveModuleUp', $moduleTwo->id)
        ->assertHasNoErrors();

    expect($moduleOne->fresh()->order)->toBe(2);
    expect($moduleTwo->fresh()->order)->toBe(1);
});

test('admin can move content including quiz content within a module from structure page', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Content Reorder Course',
        'slug' => 'content-reorder-course',
        'description' => 'Course for content reorder.',
    ]);
    $topic = Topic::create([
        'course_id' => $course->id,
        'name' => 'Topic One',
        'description' => 'Topic description.',
        'order' => 1,
    ]);
    $module = Module::create([
        'topic_id' => $topic->id,
        'title' => 'Module One',
        'description' => 'Module description.',
        'order' => 1,
    ]);
    $videoContent = Content::create([
        'module_id' => $module->id,
        'order' => 1,
        'title' => 'Video Lesson',
        'body' => 'Video body.',
        'type' => ContentType::Video,
    ]);
    $quizContent = Content::create([
        'module_id' => $module->id,
        'order' => 2,
        'title' => 'Quiz Lesson',
        'body' => 'Quiz body.',
        'type' => ContentType::Quiz,
    ]);

    Livewire::actingAs($admin)
        ->test(StructureCourse::class, ['course' => $course])
        ->call('moveContentUp', $quizContent->id)
        ->assertHasNoErrors();

    expect($videoContent->fresh()->order)->toBe(2);
    expect($quizContent->fresh()->order)->toBe(1);

    Livewire::actingAs($admin)
        ->test(StructureCourse::class, ['course' => $course])
        ->call('moveContentUp', $quizContent->id)
        ->assertHasNoErrors();

    expect($videoContent->fresh()->order)->toBe(2);
    expect($quizContent->fresh()->order)->toBe(1);
});

test('admin can build a quiz content assessment from the unified quiz editor', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Quiz Editor Course',
        'slug' => 'quiz-editor-course',
        'description' => 'Course for quiz editor.',
    ]);
    $topic = Topic::create([
        'course_id' => $course->id,
        'name' => 'Topic One',
        'description' => 'Topic description.',
        'order' => 1,
    ]);
    $module = Module::create([
        'topic_id' => $topic->id,
        'title' => 'Module One',
        'description' => 'Module description.',
        'order' => 1,
    ]);
    $content = Content::create([
        'module_id' => $module->id,
        'order' => 1,
        'title' => 'Quiz Lesson',
        'body' => 'Quiz body.',
        'type' => ContentType::Quiz,
    ]);

    Livewire::actingAs($admin)
        ->test(QuizEditor::class, ['course' => $course, 'content' => $content, 'placement' => 'content'])
        ->set('questions.0.question_text', 'What is the correct answer?')
        ->set('questions.0.type', 'multiple_choice')
        ->set('questions.0.options.0', 'Option A')
        ->set('questions.0.options.1', 'Option B')
        ->set('questions.0.correct_answer', '1')
        ->call('save')
        ->assertHasNoErrors();

    $quiz = Quiz::query()->where('content_id', $content->id)->where('kind', QuizKind::Content)->first();

    expect($quiz)->not->toBeNull();
    expect($quiz?->questions()->count())->toBe(1);
    expect($quiz?->questions()->first()?->options)->toBe(['Option A', 'Option B']);
    expect($quiz?->questions()->first()?->correct_answer)->toBe([1]);
});

test('admin can create a timestamped quiz for video content from the unified editor', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Timestamp Course',
        'slug' => 'timestamp-course',
        'description' => 'Course for timestamp quiz editor.',
    ]);
    $topic = Topic::create([
        'course_id' => $course->id,
        'name' => 'Topic One',
        'description' => 'Topic description.',
        'order' => 1,
    ]);
    $module = Module::create([
        'topic_id' => $topic->id,
        'title' => 'Module One',
        'description' => 'Module description.',
        'order' => 1,
    ]);
    $content = Content::create([
        'module_id' => $module->id,
        'order' => 1,
        'title' => 'Video Lesson',
        'body' => 'Video body.',
        'type' => ContentType::Video,
        'content_url' => 'https://example.com/video',
    ]);

    Livewire::actingAs($admin)
        ->test(QuizEditor::class, ['course' => $course, 'content' => $content, 'placement' => 'timestamped'])
        ->set('timestamp', '00:05:30')
        ->set('questions.0.question_text', 'Checkpoint question')
        ->set('questions.0.type', 'true_false')
        ->set('questions.0.correct_answer', 'true')
        ->call('save')
        ->assertHasNoErrors();

    $quiz = Quiz::query()->where('content_id', $content->id)->where('kind', QuizKind::Timestamped)->first();

    expect($quiz)->not->toBeNull();
    expect($quiz?->timestamp_seconds)->toBe(330);
});

test('content viewer renders timestamped quizzes without errors', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Viewer Timestamp Course',
        'slug' => 'viewer-timestamp-course',
        'description' => 'Course for content viewer.',
    ]);
    $topic = Topic::create([
        'course_id' => $course->id,
        'name' => 'Topic One',
        'description' => 'Topic description.',
        'order' => 1,
    ]);
    $module = Module::create([
        'topic_id' => $topic->id,
        'title' => 'Module One',
        'description' => 'Module description.',
        'order' => 1,
    ]);
    $content = Content::create([
        'module_id' => $module->id,
        'order' => 1,
        'title' => 'Video Lesson',
        'body' => 'Video body.',
        'type' => ContentType::Video,
        'content_url' => 'https://example.com/video',
    ]);
    $quiz = Quiz::create([
        'content_id' => $content->id,
        'kind' => QuizKind::Timestamped,
        'timestamp_seconds' => 330,
    ]);
    $quiz->questions()->create([
        'type' => 'true_false',
        'question_text' => 'Checkpoint question',
        'options' => [],
        'correct_answer' => [0],
    ]);

    $response = $this->actingAs($admin)->get(route('admin.courses.content.show', $content->id));

    $response->assertSuccessful();
    $response->assertSee('00:05:30');
    $response->assertSee('1 questions');
});
