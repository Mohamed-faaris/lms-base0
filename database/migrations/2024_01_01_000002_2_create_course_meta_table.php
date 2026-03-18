<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_meta', function (Blueprint $table) {
            $table->foreignId('course_id')->primary()->constrained()->onDelete('cascade');
            $table->string('category')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('difficulty')->nullable();
            $table->string('duration')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_meta');
    }
};
