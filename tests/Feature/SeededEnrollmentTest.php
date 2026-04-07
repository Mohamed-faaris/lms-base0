<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('database seeder enrolls all staff into demo courses', function () {
    $this->seed(DatabaseSeeder::class);

    $courseTitles = [
        'React JS Full Course 2024 - Bro Code',
        'PHP Full Course 2024 - Bro Code',
    ];

    $staffEmails = User::query()
        ->where('role', \App\Enums\Role::Staff)
        ->orderBy('email')
        ->pluck('email')
        ->all();

    $courses = Course::query()
        ->whereIn('title', $courseTitles)
        ->get()
        ->keyBy('title');

    expect($courses->keys()->all())->toMatchArray($courseTitles);

    $enrollments = Enrollment::query()
        ->with('user')
        ->whereHas('user', fn ($query) => $query->whereIn('email', $staffEmails))
        ->get()
        ->groupBy(fn (Enrollment $enrollment): string => $enrollment->user->email);

    $seededEnrollmentEmails = array_keys($enrollments->all());
    sort($seededEnrollmentEmails);
    $expectedStaffEmails = $staffEmails;
    sort($expectedStaffEmails);

    expect($expectedStaffEmails)->not->toBeEmpty();
    expect($seededEnrollmentEmails)->toBe($expectedStaffEmails);

    foreach ($staffEmails as $staffEmail) {
        $staffEnrollments = $enrollments->get($staffEmail);

        expect($staffEnrollments)->not->toBeNull();
        expect($staffEnrollments->count())->toBe(count($courseTitles));

        foreach ($courseTitles as $courseTitle) {
            $enrollment = $staffEnrollments->firstWhere('course_id', $courses[$courseTitle]->id);

            expect($enrollment)->not->toBeNull();
            expect($enrollment->enrolled_by)->not->toBeNull();
            expect($enrollment->deadline)->toBeGreaterThan(now()->timestamp);
            expect($enrollment->enrolled_at)->not->toBeNull();
        }
    }
});
