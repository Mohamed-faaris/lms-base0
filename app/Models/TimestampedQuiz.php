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
}
