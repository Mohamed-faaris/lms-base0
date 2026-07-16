<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Database\Seeders\SampleCourseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AdminCoursesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SampleCourseSeeder::class);
    }

    public function test_admin_can_view_courses_index(): void
    {
        $admin = $this->userWithRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.courses.index'));
        $response->assertStatus(200);
        $response->assertSee('Courses');
        $response->assertSee('New Course');
    }

    public function test_admin_can_view_course_curriculum(): void
    {
        $admin = $this->userWithRole('admin');
        $course = Course::firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.courses.curriculum', $course->slug));
        $response->assertStatus(200);
        $response->assertSee($course->title);
        $response->assertSee('Curriculum');
        $response->assertSee('Edit Curriculum');
        $response->assertSee('Back to Courses');
    }

    public function test_admin_can_view_course_edit(): void
    {
        $admin = $this->userWithRole('admin');
        $course = Course::firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.courses.edit', $course->slug));
        $response->assertStatus(200);
        $response->assertSee($course->title);
        $response->assertSee('Curriculum Editor');
        $response->assertSee('Add Module');
        $response->assertSee('Edit Course');
    }

    public function test_admin_course_index_has_edit_and_curriculum_links(): void
    {
        $admin = $this->userWithRole('admin');
        $course = Course::firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.courses.index'));
        $response->assertStatus(200);

        $content = $response->getContent();

        $this->assertStringContainsString(route('admin.courses.edit', $course->slug), $content);
        $this->assertStringContainsString(route('admin.courses.curriculum', $course->slug), $content);
    }

    public function test_admin_course_index_renders_clickable_curriculum_link(): void
    {
        $admin = $this->userWithRole('admin');
        $course = Course::firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.courses.index'));
        $response->assertStatus(200);

        $content = $response->getContent();
        $expectedHref = route('admin.courses.curriculum', $course->slug);

        $this->assertStringContainsString('href="'.$expectedHref.'"', $content);
        $this->assertStringContainsString($course->title, $content);

        $this->assertStringNotContainsString('wire:navigate.hover', $content);
    }

    public function test_learner_cannot_access_admin_courses(): void
    {
        $learner = $this->userWithRole('learner');

        $response = $this->actingAs($learner)->get(route('admin.courses.index'));
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_courses(): void
    {
        auth()->logout();

        $response = $this->get(route('admin.courses.index'));
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_admin_index_actions_row_renders_edit_and_delete_inline(): void
    {
        $admin = $this->userWithRole('admin');
        $course = Course::firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.courses.index'));
        $response->assertStatus(200);

        $rowPattern = '#<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">\s*<div class="inline-flex items-center gap-3">.*?>Edit<.*?>Delete<#s';
        $this->assertMatchesRegularExpression($rowPattern, $response->getContent());

        $this->assertStringContainsString(route('admin.courses.edit', $course->slug), $response->getContent());
        $this->assertStringContainsString('wire:click="deleteCourse('.$course->id.')"', $response->getContent());
    }

    public function test_admin_index_delete_soft_deletes_course(): void
    {
        $admin = $this->userWithRole('admin');
        $course = Course::firstOrFail();
        $courseId = $course->id;

        $this->actingAs($admin);

        Volt::test('pages.admin.courses.index')
            ->call('deleteCourse', $courseId);

        $this->assertSoftDeleted('courses', ['id' => $courseId]);

        $this->assertDatabaseHas('courses', ['id' => $courseId]);
    }

    private function userWithRole(string $role): User
    {
        return User::role($role)->firstOrFail();
    }
}
