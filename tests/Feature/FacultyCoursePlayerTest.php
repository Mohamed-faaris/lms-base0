<?php

use App\Livewire\Faculty\CoursePlayer;
use App\Models\Comment;
use App\Models\Content;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Topic;
use App\Models\User;
use Livewire\Livewire;

function buildCoursePlayerFixture(): array
{
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
        'content_meta' => [
            'youtube_id' => 'test123',
            'watch_requirement_percent' => 92,
        ],
    ]);

    Enrollment::create([
        'user_id' => $staff->id,
        'course_id' => $course->id,
        'enrolled_by' => $staff->id,
        'deadline' => now()->addDays(7)->timestamp,
        'enrolled_at' => now(),
    ]);

    return [$staff, $course, $content];
}

test('faculty course player renders controlled lesson content', function () {
    [$staff, $course, $content] = buildCoursePlayerFixture();

    Livewire::actingAs($staff)
        ->test(CoursePlayer::class, ['course' => $course])
        ->assertSee('Controlled Playback')
        ->assertSee('Comments')
        ->assertSee($content->title)
        ->assertSee('Seek lock active');
});

test('faculty course player blocks quiz start until watch requirement is met', function () {
    [$staff, $course, $content] = buildCoursePlayerFixture();

    $quiz = Quiz::create([
        'content_id' => $content->id,
        'kind' => 'end',
        'score_percentage' => 80,
    ]);

    Question::create([
        'quiz_id' => $quiz->id,
        'type' => 'true_false',
        'question_text' => 'Checkpoint question',
        'options' => [],
        'correct_answer' => [0],
    ]);

    Livewire::actingAs($staff)
        ->test(CoursePlayer::class, ['course' => $course])
        ->assertSet('showQuiz', false)
        ->call('startQuiz')
        ->assertSet('showQuiz', false)
        ->call('startQuiz', true)
        ->assertSet('showQuiz', true);
});

test('faculty course player allows posting comments for the current lesson', function () {
    [$staff, $course, $content] = buildCoursePlayerFixture();

    Livewire::actingAs($staff)
        ->test(CoursePlayer::class, ['course' => $course])
        ->set('newComment', 'This lesson needs a practical code example.')
        ->call('postComment')
        ->assertSet('newComment', '')
        ->assertSee('This lesson needs a practical code example.');

    expect(Comment::query()->where('content_id', $content->id)->count())->toBe(1);
});

test('faculty course player allows replying to comments', function () {
    [$staff, $course, $content] = buildCoursePlayerFixture();

    $comment = Comment::query()->create([
        'content_id' => $content->id,
        'user_id' => $staff->id,
        'comment_text' => 'Initial discussion point.',
    ]);

    $component = Livewire::actingAs($staff)
        ->test(CoursePlayer::class, ['course' => $course])
        ->assertSet('activeReplyCommentId', null)
        ->call('toggleReplyForm', $comment->id)
        ->assertSet('activeReplyCommentId', $comment->id)
        ->set("replyDrafts.{$comment->id}", 'I agree, and here is a follow-up.')
        ->call('postReply', $comment->id)
        ->assertSet('activeReplyCommentId', null)
        ->assertSee('I agree, and here is a follow-up.');

    $reply = Comment::query()
        ->where('content_id', $content->id)
        ->where('parent_comment_id', $comment->id)
        ->firstOrFail();

    $component
        ->call('toggleReplyForm', $reply->id)
        ->assertSet('activeReplyCommentId', $reply->id)
        ->set("replyDrafts.{$reply->id}", 'Replying to the reply, YouTube-style.')
        ->call('postReply', $reply->id)
        ->assertSet('activeReplyCommentId', null)
        ->assertSee('Replying to the reply, YouTube-style.');

    expect(Comment::query()
        ->where('content_id', $content->id)
        ->where('parent_comment_id', $comment->id)
        ->count())->toBe(1);

    expect(Comment::query()
        ->where('content_id', $content->id)
        ->where('parent_comment_id', $reply->id)
        ->count())->toBe(1);
});

test('faculty course player uses real quiz questions and stores attempts', function () {
    [$staff, $course, $content] = buildCoursePlayerFixture();

    $quiz = Quiz::create([
        'content_id' => $content->id,
        'kind' => 'end',
        'score_percentage' => 50,
    ]);

    $questionOne = Question::create([
        'quiz_id' => $quiz->id,
        'type' => 'true_false',
        'question_text' => 'React is a library.',
        'options' => [],
        'correct_answer' => [0],
    ]);

    $questionTwo = Question::create([
        'quiz_id' => $quiz->id,
        'type' => 'multiple_choice',
        'question_text' => 'Which hook manages state?',
        'options' => ['useState', 'useFetch'],
        'correct_answer' => [0],
    ]);

    Livewire::actingAs($staff)
        ->test(CoursePlayer::class, ['course' => $course])
        ->call('startQuiz', true)
        ->assertSet('showQuiz', true)
        ->assertSee('React is a library.')
        ->assertSee('Which hook manages state?')
        ->call('setAnswer', (string) $questionOne->id, '0')
        ->call('setAnswer', (string) $questionTwo->id, '0')
        ->call('submitQuiz')
        ->assertSet('quizScore', 100)
        ->assertSet('quizSubmitted', true);

    expect(QuizAttempt::query()->where('quiz_id', $quiz->id)->where('user_id', $staff->id)->count())->toBe(1);
});
