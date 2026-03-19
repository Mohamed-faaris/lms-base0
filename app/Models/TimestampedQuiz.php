<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimestampedQuiz extends Model
{
    protected $table = 'timestamped_quiz';

    protected $fillable = [
        'content_id',
        'timestamp',
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
}
