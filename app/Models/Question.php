<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'type',
        'question_text',
        'options',
        'correct_answer',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'correct_answer' => 'array',
        ];
    }

    public function endQuiz()
    {
        return $this->hasMany(EndQuiz::class);
    }

    public function modelQuiz()
    {
        return $this->hasMany(ModelQuiz::class);
    }
}
