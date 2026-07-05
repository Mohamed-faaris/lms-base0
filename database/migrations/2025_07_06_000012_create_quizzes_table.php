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
            $table->foreignId('module_item_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->integer('passing_marks')->default(0);
            $table->integer('duration')->nullable()->comment('minutes');
            $table->integer('attempt_limit')->default(1);
            $table->boolean('shuffle_questions')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
