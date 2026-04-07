<?php

use App\Enums\College;
use App\Enums\Department;
use App\Livewire\Admin\Enrollments\Create as EnrollmentBatchCreate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Livewire\Livewire;

test('admin can open the enrollment batch create page', function () {
    $admin = User::factory()->admin()->create();

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.enrollments.create'));

    $response->assertSuccessful();
    $response->assertSee('Create Enrollment Batch');
});

test('all learners mode enrolls staff and faculty into one shared batch', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'All Learners Batch',
        'description' => 'All learners test.',
    ]);
    $staff = User::factory()->staff()->create();
    $faculty = User::factory()->faculty()->create();

    Livewire::actingAs($admin)
        ->test(EnrollmentBatchCreate::class)
        ->set('courseId', $course->id)
        ->set('targetMode', 'all')
        ->set('deadlineDays', '10')
        ->call('createBatch')
        ->assertSet('showSuccess', true)
        ->assertSet('createdCount', 2);

    $enrollments = Enrollment::query()
        ->where('course_id', $course->id)
        ->orderBy('user_id')
        ->get();

    expect($enrollments)->toHaveCount(2);
    expect($enrollments->pluck('batch_id')->unique())->toHaveCount(1);
    expect($enrollments->pluck('user_id')->all())->toBe([$staff->id, $faculty->id]);
});

test('college selection mode can target multiple colleges and optional departments', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'College Batch',
        'description' => 'College targeting test.',
    ]);
    $krceStaff = User::factory()->staff()->create([
        'college' => College::KRCE,
        'department' => Department::CSE,
    ]);
    $krctFaculty = User::factory()->faculty()->create([
        'college' => College::KRCT,
        'department' => Department::CSE,
    ]);
    User::factory()->staff()->create([
        'college' => College::KRCE,
        'department' => Department::ECE,
    ]);

    Livewire::actingAs($admin)
        ->test(EnrollmentBatchCreate::class)
        ->set('courseId', $course->id)
        ->set('targetMode', 'college')
        ->set('selectedColleges', [College::KRCE->value, College::KRCT->value])
        ->set('selectedDepartments', [Department::CSE->value])
        ->call('createBatch')
        ->assertSet('createdCount', 2);

    expect(Enrollment::where('course_id', $course->id)->pluck('user_id')->sort()->values()->all())
        ->toBe([$krceStaff->id, $krctFaculty->id]);
});

test('departments mode enrolls matching departments across colleges and skips duplicates', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Department Batch',
        'description' => 'Department targeting test.',
    ]);
    $krceCse = User::factory()->staff()->create([
        'college' => College::KRCE,
        'department' => Department::CSE,
    ]);
    $krctCse = User::factory()->faculty()->create([
        'college' => College::KRCT,
        'department' => Department::CSE,
    ]);
    User::factory()->staff()->create([
        'college' => College::KRCE,
        'department' => Department::ECE,
    ]);

    Enrollment::create([
        'user_id' => $krceCse->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'legacy-dept-batch',
        'deadline' => now()->addDays(5)->timestamp,
    ]);

    Livewire::actingAs($admin)
        ->test(EnrollmentBatchCreate::class)
        ->set('courseId', $course->id)
        ->set('targetMode', 'departments')
        ->set('selectedDepartments', [Department::CSE->value])
        ->call('createBatch')
        ->assertSet('createdCount', 1)
        ->assertSet('skippedCount', 1);

    expect(Enrollment::where('course_id', $course->id)->pluck('user_id')->sort()->values()->all())
        ->toBe([$krceCse->id, $krctCse->id]);
});

test('non admin users cannot access the enrollment batch create page', function () {
    $staff = User::factory()->staff()->create();

    $response = $this
        ->actingAs($staff)
        ->get(route('admin.enrollments.create'));

    $response->assertForbidden();
});
