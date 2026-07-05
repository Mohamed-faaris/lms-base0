<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('progress_id')->constrained('learning_progress')->cascadeOnDelete();
            $table->integer('last_second')->default(0);
            $table->integer('watched_seconds')->default(0);
            $table->decimal('watch_percentage', 5, 2)->default(0);
            $table->integer('seek_attempts')->default(0);
            $table->integer('pause_count')->default(0);
            $table->decimal('playback_speed', 3, 2)->default(1.00);
            $table->integer('focus_loss_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_sessions');
    }
};
