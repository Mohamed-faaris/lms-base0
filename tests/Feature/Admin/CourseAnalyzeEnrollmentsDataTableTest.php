<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\Progress;
use App\Models\Topic;
use App\Models\User;

test('course analyze enrollments datatable returns enrollment rows for the course', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::create([
        'title' => 'Datatable Course',
        'slug' => 'datatable-course',
        'description' => 'Course for datatable endpoint.',
    ]);
    $topic = Topic::create([
        'course_id' => $course->id,
        'name' => 'Topic One',
        'description' => 'Topic description.',
        'order' => 1,
    ]);
    $module = Module::create([
        'topic_id' => $topic->id,
        'title' => 'Module One',
        'description' => 'Module description.',
        'order' => 1,
    ]);

    $student = User::factory()->staff()->create([
        'name' => 'Datatable Student',
        'email' => 'datatable@example.com',
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrolled_by' => $admin->id,
        'batch_id' => 'datatable-batch',
        'deadline' => now()->addDays(7)->timestamp,
        'enrolled_at' => now(),
    ]);

    Progress::create([
        'user_id' => $student->id,
        'content_id' => $module->contents()->create([
            'order' => 1,
            'title' => 'Lesson One',
            'body' => 'Lesson body.',
            'type' => \App\Enums\ContentType::Article,
        ])->id,
    ])->update(['completed_at' => now()]);

    $response = $this->actingAs($admin)->get(route('admin.courses.analyze.datatable', $course));

    $response->assertSuccessful();
    $response->assertJsonFragment([
        'user' => 'Datatable Student',
    ]);
    $response->assertJsonFragment([
        'status' => 'Completed',
    ]);
});
