<?php

namespace Database\Factories;

use App\Models\BehavioralEvent;
use App\Models\Content;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BehavioralEventFactory extends Factory
{
    protected $model = BehavioralEvent::class;

    public function definition(): array
    {
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

        return [
            'user_id' => User::factory(),
            'content_id' => Content::factory(),
            'course_id' => Course::factory(),
            'event_type' => $this->faker->randomElement($eventTypes),
            'duration_seconds' => $this->faker->numberBetween(30, 600),
            'video_timestamp' => $this->faker->numberBetween(0, 300),
            'pause_count' => $this->faker->numberBetween(0, 5),
            'seek_position' => $this->faker->numberBetween(0, 300),
            'metadata' => [
                'note' => $this->faker->sentence(),
            ],
            'event_timestamp' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
