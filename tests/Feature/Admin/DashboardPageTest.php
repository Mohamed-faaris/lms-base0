<?php

use App\Enums\ContentType;
use App\Models\Content;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Feedback;
use App\Models\Module;
use App\Models\Progress;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\Date;

test('admin dashboard shows analytics urgent work and recent feedback', function () {
    Date::setTestNow('2026-04-08 09:00:00');

    $admin = User::factory()->admin()->create([
        'name' => 'Admin User',
    ]);
    $facultyOne = User::factory()->faculty()->create([
        'name' => 'Faculty One',
    ]);
    $facultyTwo = User::factory()->faculty()->create([
        'name' => 'Faculty Two',
    ]);
    User::factory()->staff()->create([
        'name' => 'Staff Member',
    ]);

    $courseActive = Course::create([
        'title' => 'Course Alpha',
        'description' => 'Active learning course.',
    ]);
    $courseStale = Course::create([
        'title' => 'Course Stale',
        'description' => 'Needs attention.',
    ]);

    $activeTopic = Topic::create([
        'course_id' => $courseActive->id,
        'name' => 'Alpha Topic',
        'description' => 'Topic description.',
        'order' => 1,
    ]);
    $activeModule = Module::create([
        'topic_id' => $activeTopic->id,
        'title' => 'Alpha Module',
        'description' => 'Module description.',
        'order' => 1,
    ]);
    $activeContent = Content::create([
        'module_id' => $activeModule->id,
        'order' => 1,
        'title' => 'Alpha Lesson',
        'body' => 'Lesson body.',
        'type' => ContentType::Article,
    ]);

    $staleTopic = Topic::create([
        'course_id' => $courseStale->id,
        'name' => 'Stale Topic',
        'description' => 'Topic description.',
        'order' => 1,
    ]);
    $staleModule = Module::create([
        'topic_id' => $staleTopic->id,
        'title' => 'Stale Module',
        'description' => 'Module description.',
        'order' => 1,
    ]);
    Content::create([
        'module_id' => $staleModule->id,
        'order' => 1,
        'title' => 'Stale Lesson',
        'body' => 'Lesson body.',
        'type' => ContentType::Article,
    ]);

    Enrollment::create([
        'user_id' => $facultyOne->id,
        'course_id' => $courseActive->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-ALPHA',
        'deadline' => now()->addDays(2)->timestamp,
        'enrolled_at' => now()->subDay(),
    ]);

    Enrollment::create([
        'user_id' => $facultyTwo->id,
        'course_id' => $courseStale->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-STALE',
        'deadline' => now()->subDay()->timestamp,
        'enrolled_at' => now()->subDays(2),
    ]);

    Progress::unguarded(function () use ($facultyOne, $activeContent): void {
        Progress::create([
            'user_id' => $facultyOne->id,
            'content_id' => $activeContent->id,
            'completed_at' => now()->subHours(3),
        ]);
    });

    Feedback::create([
        'user_id' => $facultyOne->id,
        'course_id' => $courseActive->id,
        'rating' => 4,
        'comments' => 'Needs work on module 2 but the rest is clear.',
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Admin Dashboard');
    $response->assertSee('Urgent Deadlines');
    $response->assertSee('Recent Enrollment Batches');
    $response->assertSee('Course Alpha');
    $response->assertSee('Course Stale');
    $response->assertSee('BATCH-ALPHA');
    $response->assertSee('BATCH-STALE');
    $response->assertSee('2 days left');
    $response->assertSee('Overdue');
    $response->assertSee('Needs work on module 2');
    $response->assertSee('4/5');

    Date::setTestNow();
});

test('admin dashboard shows empty states without crashing', function () {
    $admin = User::factory()->admin()->create();

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.dashboard'));

    $response->assertSuccessful();
    $response->assertSee('No overdue or near-term batches');
    $response->assertSee('No enrollment batches yet');
    $response->assertSee('No learner feedback has been submitted yet');
});

test('non admin users cannot access the admin dashboard', function () {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});
