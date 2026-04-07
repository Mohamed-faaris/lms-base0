<?php

use App\Livewire\Faculty\CoursePlayer;
use App\Models\Content;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\Topic;
use App\Models\User;
use Livewire\Livewire;

test('faculty course player renders enrolled course content', function () {
    $staff = User::factory()->staff()->create();
    $course = Course::create([
        'title' => 'Course Player Test',
        'description' => 'Course player test course',
    ]);

    $topic = Topic::create([
        'course_id' => $course->id,
        'name' => 'Topic 1',
        'description' => 'Topic description',
        'order' => 1,
    ]);

    $module = Module::create([
        'topic_id' => $topic->id,
        'title' => 'Module 1',
        'description' => 'Module description',
        'order' => 1,
    ]);

    $content = Content::create([
        'module_id' => $module->id,
        'order' => 1,
        'title' => 'Lesson 1',
        'body' => 'Lesson body',
        'type' => 'video',
        'content_url' => 'https://youtube.com/watch?v=test123',
        'content_meta' => ['youtube_id' => 'test123'],
    ]);

    Enrollment::create([
        'user_id' => $staff->id,
        'course_id' => $course->id,
        'enrolled_by' => $staff->id,
        'deadline' => now()->addDays(7)->timestamp,
        'enrolled_at' => now(),
    ]);

    Livewire::actingAs($staff)
        ->test(CoursePlayer::class, ['course' => $course])
        ->assertSee('Course Modules')
        ->assertSee($content->title)
        ->assertSee('Take Module Quiz');
});
