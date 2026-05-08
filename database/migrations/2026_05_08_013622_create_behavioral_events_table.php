<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('behavioral_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('content_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');

            $table->enum('event_type', [
                'session_start',
                'session_end',
                'video_play',
                'video_pause',
                'video_forward',
                'video_backward',
                'video_seek',
                'tab_switch',
                'window_blur',
                'window_focus',
                'pause_frequency',
                'video_completed',
                'quiz_started',
                'quiz_completed',
                'quiz_attempt',
                'content_revisit',
                'inactivity_detected',
                'engagement_checkpoint',
            ]);

            $table->integer('duration_seconds')->nullable();
            $table->integer('video_timestamp')->nullable();
            $table->integer('pause_count')->nullable();
            $table->integer('seek_position')->nullable();
            $table->text('metadata')->nullable();

            $table->timestamp('event_timestamp')->useCurrent();

            $table->index(['user_id', 'event_type', 'event_timestamp']);
            $table->index(['content_id', 'event_timestamp']);
            $table->index(['course_id', 'event_timestamp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('behavioral_events');
    }
};
