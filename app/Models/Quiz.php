<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'quizzes';

    protected $fillable = [
        'content_id',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function module()
    {
        return $this->hasOneThrough(Module::class, Content::class);
    }

    public function endQuizzes()
    {
        return $this->hasMany(EndQuiz::class);
    }

    public function moduleQuizzes()
    {
        return $this->hasMany(ModuleQuiz::class);
    }

    public function timestampedQuizzes()
    {
        return $this->hasMany(TimestampedQuiz::class);
    }

    public function isEndQuiz(): bool
    {
        return $this->endQuizzes()->exists();
    }

    public function isModuleQuiz(): bool
    {
        return $this->moduleQuizzes()->exists();
    }

    public function isTimestampedQuiz(): bool
    {
        return $this->timestampedQuizzes()->exists();
    }
}
