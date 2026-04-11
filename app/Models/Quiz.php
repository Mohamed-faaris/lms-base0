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
    ];

    protected function casts(): array
    {
        return [
            'kind' => QuizKind::class,
            'timestamp_seconds' => 'integer',
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
}
