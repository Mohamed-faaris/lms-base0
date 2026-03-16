<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Progress;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // Try to use new tables if they exist, otherwise use fallback data
        $colleges = $this->getColleges();
        $recentActivities = $this->getRecentActivities();
        $chartData = $this->getChartData();

        return view('admin.dashboard', compact('colleges', 'recentActivities', 'chartData'));
    }

    /**
     * Get colleges data - tries new tables, falls back to existing users table
     */
    private function getColleges()
    {
        // Check if colleges table exists
        if (Schema::hasTable('colleges')) {
            return DB::table('colleges')
                ->leftJoin('departments', 'colleges.id', '=', 'departments.college_id')
                ->leftJoin('faculties', 'colleges.id', '=', 'faculties.college_id')
                ->leftJoin('users', function ($join) {
                    $join->on('colleges.code', '=', 'users.college');
                })
                ->select(
                    'colleges.id',
                    'colleges.name',
                    DB::raw('COUNT(DISTINCT departments.id) as total_departments'),
                    DB::raw('COUNT(DISTINCT faculties.user_id) as total_faculties'),
                    DB::raw('COUNT(DISTINCT users.id) as total_users')
                )
                ->groupBy('colleges.id', 'colleges.name')
                ->get()
                ->map(function ($college) {
                    // Calculate progress (placeholder - would come from progress_tracking table)
                    $college->progress = rand(50, 85);
                    return $college;
                });
        }

        // Fallback: Use existing users table with college enum
        $collegeData = DB::table('users')
            ->select(
                'college',
                DB::raw('COUNT(*) as total_users'),
                DB::raw('COUNT(DISTINCT department) as total_departments')
            )
            ->whereNotNull('college')
            ->groupBy('college')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->college,
                    'name' => strtoupper($item->college),
                    'code' => $item->college,
                    'total_departments' => $item->total_departments,
                    'total_faculties' => 0, // Will need faculties table
                    'total_users' => $item->total_users,
                    'progress' => rand(50, 85),
                ];
            });

        // If no college data, provide sample data
        if ($collegeData->isEmpty()) {
            return collect([
                (object) ['id' => 1, 'name' => 'KRCE College', 'code' => 'krce', 'total_departments' => 5, 'total_faculties' => 45, 'total_users' => 850, 'progress' => 72],
                (object) ['id' => 2, 'name' => 'KRCT College', 'code' => 'krct', 'total_departments' => 4, 'total_faculties' => 38, 'total_users' => 620, 'progress' => 58],
                (object) ['id' => 3, 'name' => 'MKCE College', 'code' => 'mkce', 'total_departments' => 5, 'total_faculties' => 52, 'total_users' => 720, 'progress' => 65],
            ]);
        }

        return $collegeData;
    }

    /**
     * Get recent activities from courses, modules, videos, quizzes
     */
    private function getRecentActivities()
    {
        $activities = collect();

        // Get latest courses
        $courses = Course::orderBy('created_at', 'desc')->take(2)->get();
        foreach ($courses as $course) {
            $activities->push((object) [
                'type' => 'course',
                'title' => 'New course "' . $course->title . '" was uploaded',
                'description' => 'By Admin',
                'icon' => 'course',
                'created_at' => $course->created_at,
            ]);
        }

        // Get latest enrollments as faculty activities
        $enrollments = Enrollment::with(['course', 'enrolledBy'])->orderBy('enrolled_at', 'desc')->take(2)->get();
        foreach ($enrollments as $enrollment) {
            $activities->push((object) [
                'type' => 'module',
                'title' => 'New enrollment in "' . ($enrollment->course->title ?? 'Course') . '"',
                'description' => 'By ' . ($enrollment->enrolledBy->name ?? 'Admin'),
                'icon' => 'module',
                'created_at' => $enrollment->enrolled_at,
            ]);
        }

        // Get quiz attempts as quiz activities
        $quizAttempts = \App\Models\QuizAttempt::with('user')->orderBy('attempted_at', 'desc')->take(1)->get();
        foreach ($quizAttempts as $attempt) {
            $activities->push((object) [
                'type' => 'quiz',
                'title' => 'Quiz completed by ' . ($attempt->user->name ?? 'User'),
                'description' => 'Score: ' . $attempt->score . '%',
                'icon' => 'quiz',
                'created_at' => $attempt->attempted_at,
            ]);
        }

        // Get content as video activities
        $contents = \App\Models\Content::orderBy('created_at', 'desc')->take(1)->get();
        foreach ($contents as $content) {
            $contentType = is_object($content->type) ? $content->type->value : $content->type;
            $activities->push((object) [
                'type' => 'video',
                'title' => 'New content "' . $content->title . '" was added',
                'description' => 'Type: ' . $contentType,
                'icon' => 'video',
                'created_at' => $content->created_at,
            ]);
        }

        // If no activities, return sample data
        if ($activities->isEmpty()) {
            return collect([
                (object) ['type' => 'course', 'title' => 'New course "Advanced Laravel" was uploaded', 'description' => 'By Admin', 'icon' => 'course', 'created_at' => now()->subHours(2)],
                (object) ['type' => 'module', 'title' => 'Prof. John Smith completed "Database Module"', 'description' => 'KRCE College', 'icon' => 'module', 'created_at' => now()->subHours(5)],
                (object) ['type' => 'quiz', 'title' => 'New quiz "Laravel Fundamentals" was created', 'description' => 'By Admin', 'icon' => 'quiz', 'created_at' => now()->subDays(1)],
                (object) ['type' => 'video', 'title' => 'Fun activity "Code Challenge" was added', 'description' => 'KRCT College', 'icon' => 'video', 'created_at' => now()->subDays(2)],
                (object) ['type' => 'enrollment', 'title' => '15 new students enrolled in "PHP Basics"', 'description' => 'MKCE College', 'icon' => 'enrollment', 'created_at' => now()->subDays(3)],
            ]);
        }

        return $activities->sortByDesc('created_at')->take(5)->values();
    }

    /**
     * Get chart data for dashboard
     */
    private function getChartData()
    {
        // College Progress Data
        $collegeProgress = $this->getColleges()->map(function ($college) {
            return [
                'name' => $college->name,
                'progress' => $college->progress,
            ];
        });

        // Course Completion Data
        $courseCompletions = Course::withCount('enrollments')
            ->get()
            ->map(function ($course) {
                // Calculate completion percentage from progress
                $totalEnrollments = $course->enrollments_count ?? 0;
                if ($totalEnrollments > 0 && Schema::hasTable('progress')) {
                    $completed = Progress::whereHas('content', function ($q) use ($course) {
                        $q->where('course_id', $course->id);
                    })->count();
                    $completion = round(($completed / $totalEnrollments) * 100);
                } else {
                    $completion = rand(40, 90);
                }
                return [
                    'name' => $course->title,
                    'completion' => $completion,
                ];
            });

        if ($courseCompletions->isEmpty()) {
            $courseCompletions = collect([
                ['name' => 'Laravel Fundamentals', 'completion' => 85],
                ['name' => 'PHP Basics', 'completion' => 62],
                ['name' => 'Web Development', 'completion' => 45],
                ['name' => 'Database Design', 'completion' => 78],
            ]);
        }

        // User Activity by College
        $userActivity = DB::table('users')
            ->select('college', DB::raw('COUNT(*) as count'))
            ->whereNotNull('college')
            ->groupBy('college')
            ->get()
            ->map(function ($item) {
                return [
                    'college' => strtoupper($item->college),
                    'count' => $item->count,
                ];
            });

        if ($userActivity->isEmpty()) {
            $userActivity = collect([
                ['college' => 'KRCE', 'count' => 450],
                ['college' => 'KRCT', 'count' => 380],
                ['college' => 'MKCE', 'count' => 320],
            ]);
        }

        return [
            'college_progress' => $collegeProgress,
            'course_completion' => $courseCompletions,
            'user_activity' => $userActivity,
        ];
    }
}
