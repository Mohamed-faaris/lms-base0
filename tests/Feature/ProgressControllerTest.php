<?php

use App\Models\Content;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\Progress;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('progress update creates new record with tracking columns on first request', function () {
    $user = User::factory()->staff()->create();
    $course = Course::create(['title' => 'Test Course', 'description' => 'Test']);

    $topic = Topic::create([
        'course_id' => $course->id,
        'name' => 'Test Topic',
        'description' => 'Test',
        'order' => 1,
    ]);

    $module = Module::create([
        'topic_id' => $topic->id,
        'title' => 'Test Module',
        'description' => 'Test',
        'order' => 1,
    ]);

    $content = Content::create([
        'module_id' => $module->id,
        'order' => 1,
        'title' => 'Video',
        'body' => 'Test',
        'type' => 'video',
        'content_url' => 'https://youtube.com/watch?v=test123',
        'content_meta' => ['youtube_id' => 'test123'],
    ]);

    Enrollment::create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'enrolled_by' => $user->id,
        'deadline' => now()->addDays(7)->timestamp,
        'enrolled_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->postJson('/progress/update', [
            'module_id' => $content->id,
            'seconds' => 45,
            'duration' => 900,
        ]);

    $response->assertSuccessful();
    $response->assertJson(['success' => true]);

    $progress = Progress::where('user_id', $user->id)
        ->where('content_id', $content->id)
        ->first();

    expect($progress)->not->toBeNull();
    expect($progress->progress_seconds)->toBe(45);
    expect($progress->video_duration)->toBe(900);
    expect($progress->last_watched_at)->not->toBeNull();
});

test('progress update correctly updates tracking columns on subsequent requests', function () {
    $user = User::factory()->staff()->create();
    $course = Course::create(['title' => 'Test Course', 'description' => 'Test']);

    $topic = Topic::create([
        'course_id' => $course->id,
        'name' => 'Test Topic',
        'description' => 'Test',
        'order' => 1,
    ]);

    $module = Module::create([
        'topic_id' => $topic->id,
        'title' => 'Test Module',
        'description' => 'Test',
        'order' => 1,
    ]);

    $content = Content::create([
        'module_id' => $module->id,
        'order' => 1,
        'title' => 'Video',
        'body' => 'Test',
        'type' => 'video',
        'content_url' => 'https://youtube.com/watch?v=test123',
        'content_meta' => ['youtube_id' => 'test123'],
    ]);

    Enrollment::create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'enrolled_by' => $user->id,
        'deadline' => now()->addDays(7)->timestamp,
        'enrolled_at' => now(),
    ]);

    // First request
    $this->actingAs($user)
        ->postJson('/progress/update', [
            'module_id' => $content->id,
            'seconds' => 30,
            'duration' => 900,
        ]);

    // Second request with higher progress
    $response = $this->actingAs($user)
        ->postJson('/progress/update', [
            'module_id' => $content->id,
            'seconds' => 120,
            'duration' => 900,
        ]);

    $response->assertSuccessful();

    $progress = Progress::where('user_id', $user->id)
        ->where('content_id', $content->id)
        ->first();

    expect($progress->progress_seconds)->toBe(120);
    expect($progress->video_duration)->toBe(900);
});
