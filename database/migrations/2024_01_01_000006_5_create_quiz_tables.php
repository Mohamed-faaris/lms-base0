<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained()->onDelete('cascade');
            $table->string('kind');
            $table->integer('timestamp_seconds')->nullable();
            $table->unsignedTinyInteger('score_percentage')->nullable();
            $table->timestamps();

            $table->index('content_id');
            $table->index('kind');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
