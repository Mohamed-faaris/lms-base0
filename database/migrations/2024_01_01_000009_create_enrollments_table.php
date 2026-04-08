<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('enrolled_by')->constrained('users');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('batch_id')->nullable()->index();
            $table->integer('deadline')->default(0);
            $table->timestamp('enrolled_at')->useCurrent();

            $table->primary(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
