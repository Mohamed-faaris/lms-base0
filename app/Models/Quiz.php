<?php

namespace App\Models;

use App\Enums\QuizKind;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'quizzes';

    protected $fillable = [
        'content_id',
        'kind',
        'timestamp_seconds',
        'score_percentage',
    ];

    protected function casts(): array
    {
        return [
            'kind' => QuizKind::class,
            'timestamp_seconds' => 'integer',
            'score_percentage' => 'integer',
        ];
    }

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function isContentQuiz(): bool
    {
        return $this->kind === QuizKind::Content;
    }

    public function isEndQuiz(): bool
    {
        return $this->kind === QuizKind::End;
    }

    public function isTimestampedQuiz(): bool
    {
        return $this->kind === QuizKind::Timestamped;
    }

    public function passingScore(): int
    {
        return max(0, min(100, $this->score_percentage ?? 0));
    }
}
