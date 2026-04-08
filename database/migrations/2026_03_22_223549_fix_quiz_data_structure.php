<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('questions', 'quiz_id')) {
            Schema::table('questions', function (Blueprint $table): void {
                $table->foreignId('quiz_id')->nullable()->after('id')->constrained('quizzes')->cascadeOnDelete();
            });
        }

        foreach (DB::table('quizzes')->select('id', 'question_id')->get() as $quiz) {
            if ($quiz->question_id) {
                DB::table('questions')
                    ->where('id', $quiz->question_id)
                    ->update(['quiz_id' => $quiz->id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('quiz_id');
        });
    }
};
