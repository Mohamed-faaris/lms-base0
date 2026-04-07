<?php

use App\Livewire\Faculty\Courses as FacultyCourses;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Livewire\Livewire;

test('faculty courses page shows normalized deadline labels for legacy and timestamp enrollments', function () {
    Date::setTestNow('2026-04-07 09:00:00');

    $staff = User::factory()->staff()->create();

    $legacyCourse = Course::create([
        'title' => 'Legacy Deadline Course',
        'description' => 'Legacy enrollment deadline format.',
    ]);

    $timestampCourse = Course::create([
        'title' => 'Timestamp Deadline Course',
        'description' => 'Timestamp enrollment deadline format.',
    ]);

    $overdueCourse = Course::create([
        'title' => 'Overdue Deadline Course',
        'description' => 'Expired enrollment deadline format.',
    ]);

    Enrollment::create([
        'user_id' => $staff->id,
        'course_id' => $legacyCourse->id,
        'enrolled_by' => $staff->id,
        'deadline' => 30,
        'enrolled_at' => now(),
    ]);

    Enrollment::create([
        'user_id' => $staff->id,
        'course_id' => $timestampCourse->id,
        'enrolled_by' => $staff->id,
        'deadline' => now()->addDays(2)->timestamp,
        'enrolled_at' => now(),
    ]);

    Enrollment::create([
        'user_id' => $staff->id,
        'course_id' => $overdueCourse->id,
        'enrolled_by' => $staff->id,
        'deadline' => now()->subDays(1)->timestamp,
        'enrolled_at' => now(),
    ]);

    Livewire::actingAs($staff)
        ->test(FacultyCourses::class)
        ->assertSee('30 days left')
        ->assertSee('2 days left')
        ->assertSee('Overdue');

    Date::setTestNow();
});

test('faculty courses page renders empty state without crashing', function () {
    $staff = User::factory()->staff()->create();

    Livewire::actingAs($staff)
        ->test(FacultyCourses::class)
        ->assertSee('No courses enrolled yet')
        ->assertSee('Return to Dashboard');
});
