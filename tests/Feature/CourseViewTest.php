<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\SampleCourseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseViewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SampleCourseSeeder::class);
    }

    public function test_learner_can_view_course(): void
    {
        $user = User::where('email', 'learner@campus.edu')->firstOrFail();

        $response = $this->actingAs($user)->get('/learner/my-learning/cs101-computer-science-fundamentals');

        $response->assertStatus(200);
    }
}
