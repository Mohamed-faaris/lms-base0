<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('progress', function (Blueprint $table) {
            $table->integer('progress_seconds')->default(0)->after('completed_at');
            $table->integer('video_duration')->nullable()->after('progress_seconds');
            $table->timestamp('last_watched_at')->nullable()->after('video_duration');
        });
    }

    public function down(): void
    {
        Schema::table('progress', function (Blueprint $table) {
            $table->dropColumn(['progress_seconds', 'video_duration', 'last_watched_at']);
        });
    }
};
