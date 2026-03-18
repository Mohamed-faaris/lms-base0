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
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index('content_id');
        });

        Schema::create('module_quiz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('timestamped_quiz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained()->onDelete('cascade');
            $table->integer('timestamp'); // in seconds
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index('content_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timestamped_quiz');
        Schema::dropIfExists('module_quiz');
        Schema::dropIfExists('quizzes');
    }
};
