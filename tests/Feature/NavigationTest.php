<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\SampleCourseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SampleCourseSeeder::class);
    }

    public function test_admin_sees_admin_links(): void
    {
        $admin = $this->userWithRole('admin');

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);

        $content = $response->getContent();

        $this->assertStringContainsString(route('admin.courses.index'), $content);
    }

    public function test_learner_sees_learner_links(): void
    {
        $learner = $this->userWithRole('learner');

        $response = $this->actingAs($learner)->get('/dashboard');
        $response->assertStatus(200);

        $content = $response->getContent();

        $this->assertStringContainsString(route('learner.my-learning'), $content);
    }

    public function test_learner_does_not_see_admin_links(): void
    {
        $learner = $this->userWithRole('learner');

        $response = $this->actingAs($learner)->get('/dashboard');
        $content = $response->getContent();

        $this->assertStringNotContainsString(route('admin.courses.index'), $content);
    }

    public function test_admin_does_not_see_learner_links(): void
    {
        $admin = $this->userWithRole('admin');

        $response = $this->actingAs($admin)->get('/dashboard');
        $content = $response->getContent();

        $this->assertStringNotContainsString(route('learner.my-learning'), $content);
    }

    public function test_manager_sees_default_when_no_manager_routes_registered(): void
    {
        $manager = $this->userWithRole('manager');

        $response = $this->actingAs($manager)->get('/dashboard');
        $response->assertStatus(200);

        $content = $response->getContent();

        $this->assertStringNotContainsString(route('admin.courses.index'), $content);
        $this->assertStringNotContainsString(route('learner.my-learning'), $content);
        $this->assertStringContainsString(route('dashboard'), $content);
    }

    public function test_guest_does_not_see_role_links(): void
    {
        auth()->logout();

        $response = $this->get('/login');
        $response->assertStatus(200);

        $content = $response->getContent();

        $this->assertStringNotContainsString(route('admin.courses.index'), $content);
        $this->assertStringNotContainsString(route('learner.my-learning'), $content);
    }

    public function test_nav_filters_unregistered_routes(): void
    {
        $this->assertFalse(Route::has('admin.dashboard'));
        $this->assertFalse(Route::has('admin.settings.index'));

        $admin = $this->userWithRole('admin');

        $response = $this->actingAs($admin)->get('/dashboard');
        $content = $response->getContent();

        $this->assertStringNotContainsString('/admin/settings', $content);
        $this->assertStringNotContainsString('/admin/organizations', $content);
        $this->assertStringNotContainsString('>Settings<', $content);
        $this->assertStringNotContainsString('>Organizations<', $content);
    }

    private function userWithRole(string $role): User
    {
        return User::role($role)->firstOrFail();
    }
}
