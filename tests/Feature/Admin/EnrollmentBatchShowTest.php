<?php

use App\Livewire\Admin\Enrollments\Show as EnrollmentBatchShow;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Livewire\Livewire;

test('admin can view an individual enrollment batch page', function () {
    Date::setTestNow('2026-04-07 09:00:00');

    $admin = User::factory()->admin()->create([
        'name' => 'Admin User',
    ]);
    $firstLearner = User::factory()->staff()->create([
        'name' => 'Ada Learner',
        'email' => 'ada@example.com',
    ]);
    $secondLearner = User::factory()->faculty()->create([
        'name' => 'Ben Learner',
        'email' => 'ben@example.com',
    ]);
    $course = Course::create([
        'title' => 'Advanced Safety Training',
        'description' => 'Course for enrollment batch detail.',
    ]);

    Enrollment::create([
        'user_id' => $firstLearner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-001',
        'deadline' => now()->addDays(4)->timestamp,
        'enrolled_at' => now(),
    ]);

    Enrollment::create([
        'user_id' => $secondLearner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-001',
        'deadline' => now()->addDays(4)->timestamp,
        'enrolled_at' => now(),
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.enrollments.show', 'BATCH-001'));

    $response->assertSuccessful();
    $response->assertSee('Enrollment Batch');
    $response->assertSee('BATCH-001');
    $response->assertSee('Advanced Safety Training');
    $response->assertSee('Ada Learner');
    $response->assertSee('Ben Learner');
    $response->assertSee('Revoke Entire Batch');
    $response->assertSee('Edit Batch');

    Date::setTestNow();
});

test('admin can update the batch reference and deadline', function () {
    Date::setTestNow('2026-04-07 09:00:00');

    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Batch Edit Course',
        'description' => 'Course for editing a batch.',
    ]);
    $learner = User::factory()->staff()->create();

    Enrollment::create([
        'user_id' => $learner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-001',
        'deadline' => now()->addDays(2)->timestamp,
        'enrolled_at' => now(),
    ]);

    Livewire::actingAs($admin)
        ->test(EnrollmentBatchShow::class, ['batchKey' => 'BATCH-001'])
        ->set('batchIdInput', 'BATCH-UPDATED')
        ->set('deadlineDays', '10')
        ->call('saveBatchChanges')
        ->assertRedirect(route('admin.enrollments.show', 'BATCH-UPDATED'));

    expect(Enrollment::query()->where('course_id', $course->id)->value('batch_id'))->toBe('BATCH-UPDATED');
    expect(Enrollment::query()->where('course_id', $course->id)->value('deadline'))->toBe(now()->addDays(10)->timestamp);

    Date::setTestNow();
});

test('admin can search learners and update a single learner deadline', function () {
    Date::setTestNow('2026-04-07 09:00:00');

    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Learner Search Course',
        'description' => 'Course for learner search and deadline updates.',
    ]);
    $firstLearner = User::factory()->staff()->create([
        'name' => 'Ada Learner',
        'email' => 'ada@example.com',
    ]);
    $secondLearner = User::factory()->faculty()->create([
        'name' => 'Ben Learner',
        'email' => 'ben@example.com',
    ]);

    Enrollment::create([
        'user_id' => $firstLearner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-SEARCH',
        'deadline' => now()->addDays(3)->timestamp,
        'enrolled_at' => now(),
    ]);

    Enrollment::create([
        'user_id' => $secondLearner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-SEARCH',
        'deadline' => now()->addDays(6)->timestamp,
        'enrolled_at' => now(),
    ]);

    Livewire::actingAs($admin)
        ->test(EnrollmentBatchShow::class, ['batchKey' => 'BATCH-SEARCH'])
        ->set('learnerSearch', 'Ada')
        ->assertSee('Ada Learner')
        ->assertDontSee('Ben Learner')
        ->set('learnerDeadlineDays.'.$firstLearner->id, '12')
        ->call('saveLearnerDeadline', $firstLearner->id)
        ->assertRedirect(route('admin.enrollments.show', 'BATCH-SEARCH'));

    expect(Enrollment::query()
        ->where('user_id', $firstLearner->id)
        ->where('course_id', $course->id)
        ->value('deadline'))->toBe(now()->addDays(12)->timestamp);

    expect(Enrollment::query()
        ->where('user_id', $secondLearner->id)
        ->where('course_id', $course->id)
        ->value('deadline'))->toBe(now()->addDays(6)->timestamp);

    Date::setTestNow();
});

test('admin can revoke an individual learner from a batch', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Batch Revoke Course',
        'description' => 'Course for learner revocation.',
    ]);
    $firstLearner = User::factory()->staff()->create();
    $secondLearner = User::factory()->faculty()->create();

    Enrollment::create([
        'user_id' => $firstLearner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-001',
        'deadline' => now()->addDays(5)->timestamp,
        'enrolled_at' => now(),
    ]);

    Enrollment::create([
        'user_id' => $secondLearner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-001',
        'deadline' => now()->addDays(5)->timestamp,
        'enrolled_at' => now(),
    ]);

    Livewire::actingAs($admin)
        ->test(EnrollmentBatchShow::class, ['batchKey' => 'BATCH-001'])
        ->call('revokeLearner', $firstLearner->id)
        ->assertRedirect(route('admin.enrollments.show', 'BATCH-001'));

    expect(Enrollment::query()->where('course_id', $course->id)->pluck('user_id')->all())
        ->toBe([$secondLearner->id]);
});

test('admin can revoke an entire batch', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Whole Batch Revoke Course',
        'description' => 'Course for whole batch revocation.',
    ]);
    $firstLearner = User::factory()->staff()->create();
    $secondLearner = User::factory()->faculty()->create();

    Enrollment::create([
        'user_id' => $firstLearner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-001',
        'deadline' => now()->addDays(5)->timestamp,
        'enrolled_at' => now(),
    ]);

    Enrollment::create([
        'user_id' => $secondLearner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-001',
        'deadline' => now()->addDays(5)->timestamp,
        'enrolled_at' => now(),
    ]);

    Livewire::actingAs($admin)
        ->test(EnrollmentBatchShow::class, ['batchKey' => 'BATCH-001'])
        ->call('revokeBatch')
        ->assertRedirect(route('admin.enrollments.index'));

    expect(Enrollment::query()->where('batch_id', 'BATCH-001')->count())->toBe(0);
});
