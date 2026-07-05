<?php

use App\Enums\ProgressStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('course_enrollments')->cascadeOnDelete();
            $table->foreignId('module_item_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default(ProgressStatus::NOT_STARTED->value);
            $table->decimal('progress', 5, 2)->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_spent')->default(0)->comment('seconds');
            $table->integer('score')->nullable();
            $table->timestamps();

            $table->unique(['enrollment_id', 'module_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_progress');
    }
};
