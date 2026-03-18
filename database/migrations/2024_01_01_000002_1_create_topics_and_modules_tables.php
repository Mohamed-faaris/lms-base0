<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_id');
            $table->text('name');
            $table->text('description')->nullable();
            $table->integer('order');
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->index('course_id');
        });

        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('topic_id');
            $table->text('title');
            $table->text('description')->nullable();
            $table->integer('order');
            $table->timestamps();

            $table->foreign('topic_id')->references('id')->on('topics')->onDelete('cascade');
            $table->index('topic_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
        Schema::dropIfExists('topics');
    }
};
