<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelQuiz extends Model
{
    protected $table = 'model_quiz';

    protected $fillable = [
        'content_id',
        'timestamp',
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
}
