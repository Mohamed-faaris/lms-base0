<?php

namespace Database\Seeders;

use App\Models\BehavioralEvent;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

class BehavioralEventsSeeder extends Seeder
{
    public function run(): void
    {
        $facultyEmails = [
            'sarah@example.com',
            'michael@example.com',
            'emily@example.com',
            'david@example.com',
            'jennifer@example.com',
        ];

        $eventTypes = [
            'video_play',
            'video_pause',
            'video_seek',
            'video_completed',
            'session_start',
            'session_end',
            'tab_switch',
            'window_blur',
            'content_revisit',
            'quiz_started',
            'quiz_completed',
        ];

        foreach ($facultyEmails as $email) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                echo "User not found: $email\n";

                continue;
            }

            $enrollments = Enrollment::where('user_id', $user->id)->with('course')->get();

            foreach ($enrollments as $enrollment) {
                $course = $enrollment->course;

                if (! $course) {
                    continue;
                }

                $moduleCount = $course->modules()->count();

                if ($moduleCount === 0) {
                    continue;
                }

                $sessionCount = rand(3, 10);
                $activeModules = $course->modules()->inRandomOrder()->take(rand(1, min(3, $moduleCount)))->get();

                echo "Seeding {$sessionCount} sessions for {$user->name} in {$course->title}...\n";

                foreach (range(1, $sessionCount) as $sessionNum) {
                    $daysAgo = rand(1, 30);
                    $sessionStart = now()->subDays($daysAgo)->subHours(rand(1, 23))->subMinutes(rand(0, 59));

                    $contentId = $activeModules->random()?->id;
                    $sessionId = "{$user->id}-{$course->id}-{$sessionNum}";

                    BehavioralEvent::create([
                        'user_id' => $user->id,
                        'content_id' => $contentId,
                        'course_id' => $course->id,
                        'event_type' => 'session_start',
                        'duration_seconds' => rand(300, 3600),
                        'video_timestamp' => 0,
                        'pause_count' => 0,
                        'seek_position' => null,
                        'metadata' => ['session' => $sessionNum],
                        'event_timestamp' => $sessionStart,
                    ]);

                    $playTimestamp = $sessionStart->copy()->addMinutes(rand(1, 10));
                    BehavioralEvent::create([
                        'user_id' => $user->id,
                        'content_id' => $contentId,
                        'course_id' => $course->id,
                        'event_type' => 'video_play',
                        'duration_seconds' => rand(60, 600),
                        'video_timestamp' => 0,
                        'pause_count' => 0,
                        'seek_position' => null,
                        'metadata' => ['module' => $activeModules->first()?->title],
                        'event_timestamp' => $playTimestamp,
                    ]);

                    $pauseCount = rand(0, 4);
                    if ($pauseCount > 0) {
                        $pauseTimestamp = $sessionStart->copy()->addMinutes(rand(15, 40));
                        BehavioralEvent::create([
                            'user_id' => $user->id,
                            'content_id' => $contentId,
                            'course_id' => $course->id,
                            'event_type' => 'video_pause',
                            'duration_seconds' => rand(120, 900),
                            'video_timestamp' => rand(30, 300),
                            'pause_count' => $pauseCount,
                            'seek_position' => null,
                            'metadata' => ['pause_number' => $pauseCount],
                            'event_timestamp' => $pauseTimestamp,
                        ]);
                    }

                    if (rand(0, 100) < 60) {
                        $seekTimestamp = $sessionStart->copy()->addMinutes(rand(10, 25));
                        BehavioralEvent::create([
                            'user_id' => $user->id,
                            'content_id' => $contentId,
                            'course_id' => $course->id,
                            'event_type' => 'video_seek',
                            'duration_seconds' => rand(180, 1200),
                            'video_timestamp' => rand(60, 400),
                            'pause_count' => $pauseCount,
                            'seek_position' => rand(0, 100),
                            'metadata' => [],
                            'event_timestamp' => $seekTimestamp,
                        ]);
                    }

                    if (rand(0, 100) < 70) {
                        $tabSwitchTimestamp = $sessionStart->copy()->addMinutes(rand(20, 45));
                        BehavioralEvent::create([
                            'user_id' => $user->id,
                            'content_id' => $contentId,
                            'course_id' => $course->id,
                            'event_type' => 'tab_switch',
                            'duration_seconds' => rand(5, 30),
                            'video_timestamp' => rand(100, 400),
                            'pause_count' => null,
                            'seek_position' => null,
                            'metadata' => ['note' => 'User switched tab'],
                            'event_timestamp' => $tabSwitchTimestamp,
                        ]);
                    }

                    if (rand(0, 100) < 30) {
                        $blurTimestamp = $sessionStart->copy()->addMinutes(rand(25, 40));
                        BehavioralEvent::create([
                            'user_id' => $user->id,
                            'content_id' => $contentId,
                            'course_id' => $course->id,
                            'event_type' => 'window_blur',
                            'duration_seconds' => rand(10, 60),
                            'video_timestamp' => rand(150, 400),
                            'pause_count' => null,
                            'seek_position' => null,
                            'metadata' => ['note' => 'Window lost focus'],
                            'event_timestamp' => $blurTimestamp,
                        ]);
                    }

                    if (rand(0, 100) < 50) {
                        $completeTimestamp = $sessionStart->copy()->addMinutes(rand(30, 90));
                        BehavioralEvent::create([
                            'user_id' => $user->id,
                            'content_id' => $contentId,
                            'course_id' => $course->id,
                            'event_type' => 'video_completed',
                            'duration_seconds' => rand(300, 1800),
                            'video_timestamp' => rand(300, 600),
                            'pause_count' => $pauseCount,
                            'seek_position' => null,
                            'metadata' => ['module' => $activeModules->first()?->title, 'completed' => true],
                            'event_timestamp' => $completeTimestamp,
                        ]);
                    }

                    if (rand(0, 100) < 40) {
                        $quizTimestamp = $sessionStart->copy()->addMinutes(rand(40, 70));
                        BehavioralEvent::create([
                            'user_id' => $user->id,
                            'content_id' => null,
                            'course_id' => $course->id,
                            'event_type' => 'quiz_started',
                            'duration_seconds' => rand(60, 300),
                            'video_timestamp' => null,
                            'pause_count' => null,
                            'seek_position' => null,
                            'metadata' => ['quiz' => 'Module Quiz'],
                            'event_timestamp' => $quizTimestamp,
                        ]);

                        if (rand(0, 100) < 80) {
                            $quizCompleteTimestamp = $quizTimestamp->copy()->addMinutes(rand(5, 20));
                            BehavioralEvent::create([
                                'user_id' => $user->id,
                                'content_id' => null,
                                'course_id' => $course->id,
                                'event_type' => 'quiz_completed',
                                'duration_seconds' => rand(120, 600),
                                'video_timestamp' => null,
                                'pause_count' => null,
                                'seek_position' => null,
                                'metadata' => ['quiz' => 'Module Quiz', 'passed' => rand(0, 100) < 70],
                                'event_timestamp' => $quizCompleteTimestamp,
                            ]);
                        }
                    }

                    $sessionEndTimestamp = $sessionStart->copy()->addHours(rand(1, 2));
                    BehavioralEvent::create([
                        'user_id' => $user->id,
                        'content_id' => $contentId,
                        'course_id' => $course->id,
                        'event_type' => 'session_end',
                        'duration_seconds' => rand(1800, 7200),
                        'video_timestamp' => rand(300, 600),
                        'pause_count' => $pauseCount,
                        'seek_position' => null,
                        'metadata' => ['session' => $sessionNum, 'completed' => true],
                        'event_timestamp' => $sessionEndTimestamp,
                    ]);
                }
            }
        }

        echo "Behavioral events seeded successfully!\n";
    }
}
