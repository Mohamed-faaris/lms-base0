<?php

use App\Models\Comment;
use App\Models\Content;
use App\Models\Course;
use App\Models\CourseMeta;
use App\Models\EndQuiz;
use App\Models\Enrollment;
use App\Models\Feedback;
use App\Models\Module;
use App\Models\ModuleQuiz;
use App\Models\Progress;
use App\Models\Quiz;
use App\Models\SpeedLog;
use App\Models\TimestampedQuiz;
use App\Models\Topic;
use App\Models\Xp;
use App\Models\XpLog;
use Database\Seeders\DatabaseSeeder;

use function Pest\Laravel\seed;

test('java course seeder builds a full playlist backed course', function () {
    seed(DatabaseSeeder::class);

    $course = Course::query()->where('slug', 'java-tutorial-bro-code')->first();

    expect($course)->not->toBeNull();
    expect(CourseMeta::query()->where('course_id', $course->id)->exists())->toBeTrue();
    expect($course->courseMeta->category)->toBe('Programming');
    expect($course->courseMeta->data['playlist_id'])->toBe('PLZPZq0r_RZOOj_NOZYq_R2PECIMglLemc');

    $topicCount = Topic::query()->where('course_id', $course->id)->count();
    $moduleCount = Module::query()->whereHas('topic', fn ($query) => $query->where('course_id', $course->id))->count();
    $contentCount = Content::query()->whereHas('module.topic', fn ($query) => $query->where('course_id', $course->id))->count();

    expect($topicCount)->toBeGreaterThanOrEqual(9);
    expect($moduleCount)->toBeGreaterThanOrEqual(9);
    expect($contentCount)->toBeGreaterThanOrEqual(30);

    $quizCount = Quiz::query()->whereHas('content.module.topic', fn ($query) => $query->where('course_id', $course->id))->count();
    expect($quizCount)->toBeGreaterThanOrEqual($contentCount);

    expect(ModuleQuiz::query()->count())->toBeGreaterThanOrEqual(6);
    expect(EndQuiz::query()->count())->toBeGreaterThanOrEqual(1);
    expect(TimestampedQuiz::query()->count())->toBeGreaterThanOrEqual($contentCount);
    expect(Progress::query()->count())->toBeGreaterThanOrEqual(1);
    expect(Enrollment::query()->where('course_id', $course->id)->count())->toBeGreaterThanOrEqual(1);
    expect(Xp::query()->count())->toBeGreaterThanOrEqual(1);
    expect(XpLog::query()->count())->toBeGreaterThanOrEqual(1);
    expect(Comment::query()->count())->toBeGreaterThanOrEqual(1);
    expect(Feedback::query()->count())->toBeGreaterThanOrEqual(1);
    expect(SpeedLog::query()->count())->toBeGreaterThanOrEqual(1);
});
