<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EndQuiz extends Model
{
    protected $table = 'end_quiz';

    protected $fillable = [
        'content_id',
        'quiz_id',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function question()
    {
        return $this->hasOneThrough(Question::class, Quiz::class);
    }

    public function module()
    {
        return $this->hasOneThrough(Module::class, Content::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
