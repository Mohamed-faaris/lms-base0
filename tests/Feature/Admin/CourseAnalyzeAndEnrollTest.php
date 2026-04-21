<?php

use App\Enums\College;
use App\Enums\Department;
use App\Livewire\Admin\Enrollments\Create as EnrollmentBatchCreate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Livewire\Livewire;

test('course admin routes are registered', function () {
    expect(route('admin.courses.analyze', 1))->toContain('/admin/courses/1/analyze');
    expect(route('admin.courses.enroll', 1))->toContain('/admin/courses/1/enroll');
    expect(route('admin.enrollments.create'))->toContain('/admin/enrollments/create');
});

test('course enroll entry opens the full page flow with the course preselected', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Course Analyze Test',
        'description' => 'Test course',
    ]);

    $krceCse = User::factory()->staff()->create([
        'college' => College::KRCE,
        'department' => Department::CSE,
    ]);

    User::factory()->staff()->create([
        'college' => College::KRCT,
        'department' => Department::ECE,
    ]);

    Enrollment::create([
        'user_id' => $krceCse->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'legacy-batch',
        'deadline' => now()->addDays(10)->timestamp,
        'enrolled_at' => now(),
    ]);

    Livewire::actingAs($admin)
        ->test(EnrollmentBatchCreate::class, ['course' => $course])
        ->assertSet('courseId', $course->id)
        ->assertSet('courseLocked', true)
        ->assertSet('resolvedUserCount', 1)
        ->assertSee('Create Enrollment Batch')
        ->assertSee($course->title);
});

test('enroll page can target a single faculty member', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Course Enroll Test',
        'description' => 'Test course',
    ]);
    $staff = User::factory()->staff()->create([
        'college' => College::KRCE,
        'department' => Department::CSE,
    ]);

    Livewire::actingAs($admin)
        ->test(EnrollmentBatchCreate::class, ['course' => $course])
        ->set('targetMode', 'user')
        ->set('selectedUserIds', [$staff->id])
        ->set('deadlineDays', '15')
        ->call('createBatch')
        ->assertSet('showSuccess', true)
        ->assertSet('createdCount', 1);

    $enrollment = Enrollment::where('course_id', $course->id)
        ->where('user_id', $staff->id)
        ->first();

    expect($enrollment)->not->toBeNull();
    expect($enrollment->batch_id)->not->toBeNull();
});
