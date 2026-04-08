<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('database seeder enrolls all faculty and staff into demo courses', function () {
    $this->seed(DatabaseSeeder::class);

    $courseTitles = [
        'React JS Full Course 2024 - Bro Code',
        'PHP Full Course 2024 - Bro Code',
    ];

    $userEmails = User::query()
        ->whereIn('role', [\App\Enums\Role::Faculty, \App\Enums\Role::Staff])
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
        ->whereHas('user', fn ($query) => $query->whereIn('email', $userEmails))
        ->get()
        ->groupBy(fn (Enrollment $enrollment): string => $enrollment->user->email);

    $seededEnrollmentEmails = array_keys($enrollments->all());
    sort($seededEnrollmentEmails);
    $expectedUserEmails = $userEmails;
    sort($expectedUserEmails);

    expect($expectedUserEmails)->not->toBeEmpty();
    expect($seededEnrollmentEmails)->toBe($expectedUserEmails);

    foreach ($userEmails as $userEmail) {
        $userEnrollments = $enrollments->get($userEmail);

        expect($userEnrollments)->not->toBeNull();
        expect($userEnrollments->count())->toBeGreaterThanOrEqual(count($courseTitles));

        foreach ($courseTitles as $courseTitle) {
            $enrollment = $userEnrollments->firstWhere('course_id', $courses[$courseTitle]->id);

            expect($enrollment)->not->toBeNull();
            expect($enrollment->batch_id)->not->toBeNull();
            expect((int) $enrollment->batch_id)->toBeGreaterThan(0);
            expect($enrollment->enrolled_by)->not->toBeNull();
            expect($enrollment->deadline)->toBeGreaterThan(now()->timestamp);
            expect($enrollment->enrolled_at)->not->toBeNull();
        }
    }
});
