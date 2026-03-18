<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'quizzes';

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

    public function moduleQuizzes()
    {
        return $this->hasMany(ModuleQuiz::class);
    }

    public function timestampedQuizzes()
    {
        return $this->hasMany(TimestampedQuiz::class);
    }
}
