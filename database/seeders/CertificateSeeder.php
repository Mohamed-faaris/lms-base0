<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        $facultyUsers = User::where('role', 'faculty')->get();
        $courses = Course::all();

        foreach ($facultyUsers as $user) {
            $enrolledCourses = $user->enrollments()->pluck('course_id');

            foreach ($enrolledCourses as $courseId) {
                $alreadyHas = Certificate::where('user_id', $user->id)
                    ->where('course_id', $courseId)
                    ->exists();

                if ($alreadyHas) {
                    continue;
                }

                $completedContents = $user->progress()
                    ->whereHas('content', function ($query) use ($courseId) {
                        $query->whereHas('module', function ($query) use ($courseId) {
                            $query->whereHas('topic', function ($query) use ($courseId) {
                                $query->where('course_id', $courseId);
                            });
                        });
                    })
                    ->whereNotNull('completed_at')
                    ->count();

                if ($completedContents > 0) {
                    $completedAt = now()->subDays(rand(1, 30));

                    Certificate::create([
                        'user_id' => $user->id,
                        'course_id' => $courseId,
                        'certificate_id' => Certificate::generateCertificateId($user->id, $courseId, $completedAt),
                        'completed_at' => $completedAt,
                        'issued_at' => $completedAt,
                    ]);
                }
            }
        }

        $admin = User::where('role', 'admin')->first();
        if ($admin && !Certificate::where('user_id', $admin->id)->exists()) {
            foreach ($courses->take(3) as $course) {
                Certificate::create([
                    'user_id' => $admin->id,
                    'course_id' => $course->id,
                    'certificate_id' => Certificate::generateCertificateId($admin->id, $course->id, now()->subDays(rand(1, 60))),
                    'completed_at' => now()->subDays(rand(30, 60)),
                    'issued_at' => now()->subDays(rand(30, 60)),
                ]);
            }
        }
    }
}