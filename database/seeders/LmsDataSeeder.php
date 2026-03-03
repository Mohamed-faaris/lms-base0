<?php

namespace Database\Seeders;

use App\Enums\College;
use App\Enums\Department;
use App\Enums\Role;
use App\Models\Badge;
use App\Models\BadgeAssignment;
use App\Models\Content;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Progress;
use App\Models\Streak;
use App\Models\User;
use App\Models\Xp;
use Illuminate\Database\Seeder;

class LmsDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create faculty users
        $faculty1 = User::factory()->create([
            'name' => 'Dr. Sarah Johnson',
            'email' => 'sarah@example.com',
            'role' => Role::Faculty,
            'college' => College::KRCE,
            'department' => Department::CSE,
        ]);

        $faculty2 = User::factory()->create([
            'name' => 'Prof. Michael Chen',
            'email' => 'michael@example.com',
            'role' => Role::Faculty,
            'college' => College::KRCT,
            'department' => Department::ECE,
        ]);

        $faculty3 = User::factory()->create([
            'name' => 'Dr. Emily Rodriguez',
            'email' => 'emily@example.com',
            'role' => Role::Faculty,
            'college' => College::MKCE,
            'department' => Department::AI,
        ]);

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => Role::Admin,
        ]);

        // Create courses (matching lms-sample)
        $coursesData = [
            [
                'title' => 'Teaching Methodologies',
                'description' => 'Learn effective teaching strategies and methodologies for higher education',
                'contents' => [
                    'Introduction to Modern Pedagogy',
                    'Active Learning Strategies',
                    'Student-Centered Teaching',
                    'Interactive Lecture Techniques',
                    'Group Work and Collaboration',
                    'Assessment and Feedback',
                    'Differentiated Instruction',
                    'Reflective Teaching Practice',
                ],
                'xp_reward' => 500,
            ],
            [
                'title' => 'Research Ethics',
                'description' => 'Understanding ethical considerations in academic research',
                'contents' => [
                    'Introduction to Research Ethics',
                    'Informed Consent',
                    'Data Privacy and Security',
                    'Conflict of Interest',
                    'Plagiarism and Academic Integrity',
                ],
                'xp_reward' => 300,
            ],
            [
                'title' => 'Digital Pedagogy',
                'description' => 'Leveraging technology for enhanced learning experiences',
                'contents' => [
                    'Digital Learning Fundamentals',
                    'Online Course Design',
                    'Educational Technology Tools',
                    'Blended Learning Models',
                    'Virtual Classrooms',
                    'Multimedia in Education',
                    'Digital Assessment Methods',
                    'E-Learning Best Practices',
                    'Student Engagement Online',
                    'Adaptive Learning Technologies',
                ],
                'xp_reward' => 600,
            ],
            [
                'title' => 'Assessment Design',
                'description' => 'Creating effective and fair assessment strategies',
                'contents' => [
                    'Principles of Assessment',
                    'Formative vs Summative Assessment',
                    'Rubric Development',
                    'Portfolio Assessment',
                    'Peer Assessment',
                    'Alternative Assessment Methods',
                ],
                'xp_reward' => 400,
            ],
        ];

        foreach ($coursesData as $courseIdx => $courseItem) {
            $contents = $courseItem['contents'];
            $xpReward = $courseItem['xp_reward'];

            $course = Course::create([
                'title' => $courseItem['title'],
                'description' => $courseItem['description'],
            ]);

            // Create contents (modules)
            foreach ($contents as $index => $contentTitle) {
                Content::create([
                    'course_id' => $course->id,
                    'order' => $index + 1,
                    'title' => $contentTitle,
                    'body' => 'Content for '.$contentTitle,
                    'type' => 'article',
                ]);
            }

            // Enroll faculty users in this course
            $users = [
                ['user' => $faculty1, 'progress' => $courseIdx === 0 ? 75 : ($courseIdx === 1 ? 100 : ($courseIdx === 2 ? 30 : 0))],
                ['user' => $faculty2, 'progress' => $courseIdx === 0 ? 100 : ($courseIdx === 1 ? 60 : ($courseIdx === 2 ? 80 : 50))],
                ['user' => $faculty3, 'progress' => $courseIdx === 0 ? 30 : ($courseIdx === 1 ? 20 : ($courseIdx === 2 ? 10 : 15))],
            ];

            $deadlineDays = ['-20', '-10', '10', '25'][$courseIdx];

            foreach ($users as $userData) {
                $user = $userData['user'];
                $progressPercent = $userData['progress'];

                Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'enrolled_by' => $admin->id,
                    'deadline' => (int) $deadlineDays,
                    'xp_reward' => $xpReward,
                    'enrolled_at' => now()->subDays(30),
                ]);

                $courseContents = $course->contents;
                $completedCount = (int) round(($progressPercent / 100) * $courseContents->count());

                for ($i = 0; $i < $completedCount; $i++) {
                    if (isset($courseContents[$i])) {
                        Progress::create([
                            'user_id' => $user->id,
                            'content_id' => $courseContents[$i]->id,
                            'completed_at' => now()->subDays(rand(1, 20)),
                        ]);
                    }
                }
            }
        }

        // Create XP records for faculty
        Xp::create(['user_id' => $faculty1->id, 'xp' => 2450]);
        Xp::create(['user_id' => $faculty2->id, 'xp' => 1800]);
        Xp::create(['user_id' => $faculty3->id, 'xp' => 950]);

        // Create Streak records
        $streakCounts = [
            $faculty1->id => 15,
            $faculty2->id => 7,
            $faculty3->id => 3,
        ];

        foreach ($streakCounts as $userId => $count) {
            for ($i = 0; $i < min($count, 7); $i++) {
                Streak::create([
                    'user_id' => $userId,
                    'count' => $i + 1,
                    'date' => now()->subDays(6 - $i),
                ]);
            }

            if ($count > 7) {
                for ($i = 7; $i < $count; $i++) {
                    Streak::create([
                        'user_id' => $userId,
                        'count' => $i + 1,
                        'date' => now()->subDays($i + 1),
                    ]);
                }
            }
        }

        // Create Badges
        $badges = [
            [
                'title' => 'Novice',
                'description' => 'Starting your learning journey',
                'image' => 'badge-novice.png',
                'conditions' => ['min_xp' => 0],
            ],
            [
                'title' => 'Learner',
                'description' => 'Completed your first courses',
                'image' => 'badge-learner.png',
                'conditions' => ['min_xp' => 1000],
            ],
            [
                'title' => 'Scholar',
                'description' => 'Demonstrated commitment to learning',
                'image' => 'badge-scholar.png',
                'conditions' => ['min_xp' => 2000],
            ],
            [
                'title' => 'Expert',
                'description' => 'Showing expertise in multiple areas',
                'image' => 'badge-expert.png',
                'conditions' => ['min_xp' => 3500],
            ],
            [
                'title' => 'Master',
                'description' => 'Achieved mastery in faculty development',
                'image' => 'badge-master.png',
                'conditions' => ['min_xp' => 5000],
            ],
        ];

        foreach ($badges as $badgeData) {
            Badge::create($badgeData);
        }

        // Assign badges to faculty based on their XP
        $allBadges = Badge::all();

        BadgeAssignment::create([
            'badge_id' => $allBadges->where('title', 'Scholar')->first()->id,
            'user_id' => $faculty1->id,
            'assigned_at' => now()->subDays(5),
        ]);

        BadgeAssignment::create([
            'badge_id' => $allBadges->where('title', 'Learner')->first()->id,
            'user_id' => $faculty2->id,
            'assigned_at' => now()->subDays(10),
        ]);

        BadgeAssignment::create([
            'badge_id' => $allBadges->where('title', 'Novice')->first()->id,
            'user_id' => $faculty3->id,
            'assigned_at' => now()->subDays(15),
        ]);
    }
}
