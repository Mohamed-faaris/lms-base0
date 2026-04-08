<?php

use App\Enums\ContentType;
use App\Livewire\Admin\Enrollments\Show as EnrollmentBatchShow;
use App\Models\Content;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\Progress;
use App\Models\Topic;
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
    $response->assertDontSee('Edit Batch');
    $response->assertDontSee('Batch Reference');
    $response->assertSee('Are you sure you want to revoke this batch?');
    $response->assertSee('Type CONFIRM to continue');
    $response->assertSee('Progress Distribution');

    Date::setTestNow();
});

test('admin can view learner progress and batch progress distribution', function () {
    Date::setTestNow('2026-04-07 09:00:00');

    $admin = User::factory()->admin()->create();
    $firstLearner = User::factory()->staff()->create([
        'name' => 'Ada Learner',
        'email' => 'ada@example.com',
    ]);
    $secondLearner = User::factory()->faculty()->create([
        'name' => 'Ben Learner',
        'email' => 'ben@example.com',
    ]);
    $course = Course::create([
        'title' => 'Progress Tracking Course',
        'description' => 'Course for progress analytics on the batch page.',
    ]);
    $topic = Topic::create([
        'course_id' => $course->id,
        'name' => 'Topic One',
        'description' => 'Topic description',
        'order' => 1,
    ]);
    $module = Module::create([
        'topic_id' => $topic->id,
        'title' => 'Module One',
        'description' => 'Module description',
        'order' => 1,
    ]);

    $contents = collect(range(1, 4))->map(function (int $order) use ($module): Content {
        return Content::create([
            'module_id' => $module->id,
            'order' => $order,
            'title' => "Content {$order}",
            'body' => "Body {$order}",
            'type' => ContentType::Article,
        ]);
    });

    Enrollment::create([
        'user_id' => $firstLearner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-PROGRESS',
        'deadline' => now()->addDays(4)->timestamp,
        'enrolled_at' => now(),
    ]);

    Enrollment::create([
        'user_id' => $secondLearner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-PROGRESS',
        'deadline' => now()->addDays(4)->timestamp,
        'enrolled_at' => now(),
    ]);

    Progress::create([
        'user_id' => $firstLearner->id,
        'content_id' => $contents[0]->id,
        'completed_at' => now()->subDay(),
    ]);
    Progress::create([
        'user_id' => $firstLearner->id,
        'content_id' => $contents[1]->id,
        'completed_at' => now()->subHours(12),
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.enrollments.show', 'BATCH-PROGRESS'));

    $response->assertSuccessful();
    $response->assertSee('Ada Learner');
    $response->assertSee('Ben Learner');
    $response->assertSeeText('Progress');
    $response->assertSeeText('Status');
    $response->assertSeeText('50%');
    $response->assertSeeText('Not Started');
    $response->assertSeeText('0-25%');
    $response->assertSeeText('26-50%');
    $response->assertSeeText('51-75%');
    $response->assertSeeText('76-99%');
    $response->assertSeeText('100%');

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
        ->call('openLearnerDeadlineModal', $firstLearner->id)
        ->set('selectedLearnerDeadlineDays', '12')
        ->call('saveSelectedLearnerDeadline')
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

test('admin can filter and sort learners in a batch', function () {
    Date::setTestNow('2026-04-07 09:00:00');

    $admin = User::factory()->admin()->create();
    $firstLearner = User::factory()->staff()->create([
        'name' => 'Ada Learner',
        'email' => 'ada@example.com',
    ]);
    $secondLearner = User::factory()->faculty()->create([
        'name' => 'Ben Learner',
        'email' => 'ben@example.com',
    ]);
    $course = Course::create([
        'title' => 'Filter and Sort Course',
        'description' => 'Course for filter and sort coverage.',
    ]);
    $topic = Topic::create([
        'course_id' => $course->id,
        'name' => 'Topic One',
        'description' => 'Topic description',
        'order' => 1,
    ]);
    $module = Module::create([
        'topic_id' => $topic->id,
        'title' => 'Module One',
        'description' => 'Module description',
        'order' => 1,
    ]);

    $contents = collect(range(1, 4))->map(function (int $order) use ($module): Content {
        return Content::create([
            'module_id' => $module->id,
            'order' => $order,
            'title' => "Content {$order}",
            'body' => "Body {$order}",
            'type' => ContentType::Article,
        ]);
    });

    Enrollment::create([
        'user_id' => $firstLearner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-FILTER',
        'deadline' => now()->addDays(2)->timestamp,
        'enrolled_at' => now(),
    ]);

    Enrollment::create([
        'user_id' => $secondLearner->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'BATCH-FILTER',
        'deadline' => now()->addDays(8)->timestamp,
        'enrolled_at' => now(),
    ]);

    foreach ($contents->take(2) as $content) {
        Progress::create([
            'user_id' => $secondLearner->id,
            'content_id' => $content->id,
            'completed_at' => now()->subHour(),
        ]);
    }

    Livewire::actingAs($admin)
        ->test(EnrollmentBatchShow::class, ['batchKey' => 'BATCH-FILTER'])
        ->set('progressFilter', 'not-started')
        ->assertSee('Ada Learner')
        ->assertDontSee('Ben Learner')
        ->set('progressFilter', '50')
        ->assertSee('Ben Learner')
        ->assertDontSee('Ada Learner')
        ->set('progressFilter', 'all')
        ->set('sortBy', 'progress')
        ->set('sortDirection', 'desc')
        ->assertSeeInOrder(['Ben Learner', 'Ada Learner']);

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
        ->call('openRevokeBatchModal')
        ->set('revokeBatchConfirmation', 'WRONG')
        ->call('revokeBatch')
        ->assertHasErrors(['revokeBatchConfirmation'])
        ->set('revokeBatchConfirmation', 'CONFIRM')
        ->call('revokeBatch')
        ->assertRedirect(route('admin.enrollments.index'));

    expect(Enrollment::query()->where('batch_id', 'BATCH-001')->count())->toBe(0);
});
