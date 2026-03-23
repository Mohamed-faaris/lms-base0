<?php

use App\Enums\College;
use App\Enums\Department;
use App\Livewire\Admin\Courses\Enroll;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Livewire\Livewire;

test('course admin routes are registered', function () {
    expect(route('admin.courses.analyze', 1))->toContain('/admin/courses/1/analyze');
    expect(route('admin.courses.enroll', 1))->toContain('/admin/courses/1/enroll');
});

test('admin can open enroll page and preview eligible staff', function () {
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
        'deadline' => now()->addDays(10)->timestamp,
        'enrolled_at' => now(),
    ]);

    Livewire::actingAs($admin)
        ->test(Enroll::class, ['course' => $course])
        ->assertSet('enrollmentCount', 1)
        ->assertSee('Enrollment Options');
});

test('enroll page can target a single staff member', function () {
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
        ->test(Enroll::class, ['course' => $course])
        ->set('enrollType', 'individual')
        ->set('selectedUserId', $staff->id)
        ->set('deadlineDays', '15')
        ->call('enrollUsers')
        ->assertSet('showSuccess', true);

    expect(Enrollment::where('course_id', $course->id)->where('user_id', $staff->id)->exists())->toBeTrue();
});
