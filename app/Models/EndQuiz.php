<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EndQuiz extends Model
{
    protected $table = 'end_quiz';

    protected $fillable = [
        'content_id',
        'question_id',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }
}
