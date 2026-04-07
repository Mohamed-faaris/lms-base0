<?php

use App\Livewire\Admin\Enrollments as AdminEnrollmentsIndex;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Livewire\Livewire;

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
        'batch_id' => 'BATCH-001',
        'deadline' => now()->addDays(2)->timestamp,
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.enrollments.index'));

    $response->assertSuccessful();
    $response->assertSee('Enrollments');
    $response->assertSee('Ada Learner');
    $response->assertSee('Advanced Safety Training');
    $response->assertSee('Admin User');
    $response->assertSee('BATCH-001');
    $response->assertSee('1 user');
    $response->assertSee('2 days left');
    $response->assertSee('Total Batches');

    Date::setTestNow();
});

test('admin can search enrollments by batch id', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Batch Search Course',
        'description' => 'Course for batch search.',
    ]);
    $matchingLearner = User::factory()->staff()->create([
        'name' => 'Match User',
    ]);
    $otherLearner = User::factory()->faculty()->create([
        'name' => 'Other User',
    ]);

    Enrollment::create([
        'user_id' => $matchingLearner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-ALPHA',
        'deadline' => now()->addDays(5)->timestamp,
    ]);

    Enrollment::create([
        'user_id' => $otherLearner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-BETA',
        'deadline' => now()->addDays(5)->timestamp,
    ]);

    Livewire::actingAs($admin)
        ->test(AdminEnrollmentsIndex::class)
        ->set('search', 'BATCH-ALPHA')
        ->assertSee('BATCH-ALPHA')
        ->assertSee('Match User')
        ->assertDontSee('Other User');
});

test('non admin users cannot view the enrollments page', function () {
    $staff = User::factory()->staff()->create();

    $response = $this
        ->actingAs($staff)
        ->get(route('admin.enrollments.index'));

    $response->assertForbidden();
});
