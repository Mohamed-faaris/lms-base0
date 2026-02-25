<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('speed_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('content_id')->constrained()->onDelete('cascade');
            $table->enum('event', ['pause', 'stop', 'start']);
            $table->decimal('speed', 4, 2)->nullable();
            $table->timestamp('logged_at')->useCurrent();

            $table->index('user_id');
            $table->index('content_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('speed_logs');
    }
};