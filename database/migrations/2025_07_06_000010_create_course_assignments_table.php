<?php

use App\Enums\AssignmentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_version_id')->constrained()->cascadeOnDelete();
            $table->foreignId('faculty_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('deadline')->nullable();
            $table->string('status')->default(AssignmentStatus::PENDING->value);
            $table->timestamps();

            $table->index(['faculty_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_assignments');
    }
};
