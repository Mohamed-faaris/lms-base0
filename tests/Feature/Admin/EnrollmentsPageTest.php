<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\Date;

test('admin can view the enrollments page', function () {
    Date::setTestNow('2026-04-07 09:00:00');

    $admin = User::factory()->admin()->create([
        'name' => 'Admin User',
    ]);
    $learner = User::factory()->staff()->create([
        'name' => 'Ada Learner',
        'email' => 'ada@example.com',
    ]);
    $course = Course::create([
        'title' => 'Advanced Safety Training',
        'description' => 'Course for enrollment listing.',
    ]);

    Enrollment::create([
        'user_id' => $learner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'deadline' => now()->addDays(2)->timestamp,
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.enrollments.index'));

    $response->assertSuccessful();
    $response->assertSee('Enrollments');
    $response->assertSee('Ada Learner');
    $response->assertSee('ada@example.com');
    $response->assertSee('Advanced Safety Training');
    $response->assertSee('Admin User');
    $response->assertSee('2 days left');
    $response->assertSee('Urgent Deadlines');

    Date::setTestNow();
});

test('non admin users cannot view the enrollments page', function () {
    $staff = User::factory()->staff()->create();

    $response = $this
        ->actingAs($staff)
        ->get(route('admin.enrollments.index'));

    $response->assertForbidden();
});
