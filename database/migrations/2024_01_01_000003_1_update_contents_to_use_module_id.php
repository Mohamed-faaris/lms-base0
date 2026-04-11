<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('contents');

        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->integer('order');
            $table->string('title');
            $table->text('body')->nullable();
            $table->enum('type', ['video', 'article', 'ppt', 'quiz']);
            $table->string('content_url')->nullable();
            $table->json('content_meta')->nullable();
            $table->timestamps();

            $table->unique(['module_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');

        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('order');
            $table->string('title');
            $table->text('body')->nullable();
            $table->enum('type', ['video', 'article', 'ppt', 'quiz']);
            $table->string('content_url')->nullable();
            $table->json('content_meta')->nullable();
            $table->timestamps();

            $table->unique(['module_id', 'order']);
        });
    }
};
