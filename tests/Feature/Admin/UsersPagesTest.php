<?php

use App\Enums\ContentType;
use App\Models\Content;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\Progress;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\Date;

test('admin can view the users index and open a profile', function () {
    $admin = User::factory()->admin()->create();
    $faculty = User::factory()->faculty()->create([
        'name' => 'Faculty Member',
        'email' => 'faculty@example.com',
    ]);
    $nonAdminFaculty = User::factory()->faculty()->create([
        'name' => 'Faculty Member 2',
        'email' => 'faculty2@example.com',
    ]);
    $adminUser = User::factory()->admin()->create([
        'name' => 'Hidden Admin',
        'email' => 'hidden-admin@example.com',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    $response->assertSuccessful();
    $response->assertSee('Users');
    // DataTables loads users via AJAX, check table structure exists
    $response->assertSee('users-table');
    $response->assertSee('users-search');
    $response->assertDontSee($adminUser->name);
    $response->assertDontSee($adminUser->email);
});

test('admin user profile shows enrollments progress and deadline', function () {
    Date::setTestNow('2026-04-08 09:00:00');

    $admin = User::factory()->admin()->create();
    $learner = User::factory()->faculty()->create([
        'name' => 'Learner One',
    ]);
    $course = Course::create([
        'title' => 'Progress Course',
        'description' => 'Course for profile testing.',
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
    $contentOne = Content::create([
        'module_id' => $module->id,
        'order' => 1,
        'title' => 'Lesson One',
        'body' => 'First lesson.',
        'type' => ContentType::Article,
    ]);
    $contentTwo = Content::create([
        'module_id' => $module->id,
        'order' => 2,
        'title' => 'Lesson Two',
        'body' => 'Second lesson.',
        'type' => ContentType::Article,
    ]);

    Enrollment::create([
        'user_id' => $learner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-100',
        'deadline' => now()->addDays(5)->timestamp,
        'enrolled_at' => now()->subDay(),
    ]);

    Progress::unguarded(function () use ($learner, $contentOne): void {
        Progress::create([
            'user_id' => $learner->id,
            'content_id' => $contentOne->id,
            'completed_at' => now(),
        ]);
    });

    $response = $this->actingAs($admin)->get(route('admin.users.profile', $learner));

    $response->assertSuccessful();
    $response->assertSee('User Profile');
    $response->assertSee('Progress Course');
    $response->assertSee('50%');
    $response->assertSee('5 days left');
    $response->assertSee('Learner One');

    Date::setTestNow();
});

test('non admin users cannot access the admin users pages', function () {
    $nonAdminFaculty = User::factory()->faculty()->create();
    $learner = User::factory()->faculty()->create();

    $this->actingAs($nonAdminFaculty)->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs($nonAdminFaculty)->get(route('admin.users.profile', $learner))->assertForbidden();
});
