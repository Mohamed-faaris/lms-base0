<?php

namespace App\Models;

use App\Enums\ContentType;
use App\Enums\QuizKind;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'module_id',
        'order',
        'title',
        'body',
        'type',
        'content_url',
        'content_meta',
    ];

    protected function casts(): array
    {
        return [
            'type' => ContentType::class,
            'content_meta' => 'array',
        ];
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function course()
    {
        return $this->hasOneThrough(Course::class, Module::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function endQuiz()
    {
        return $this->hasOne(Quiz::class)->where('kind', QuizKind::End->value);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class)->orderBy('timestamp_seconds');
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class)->where('kind', QuizKind::Content->value);
    }

    public function contentQuiz()
    {
        return $this->hasOne(Quiz::class)->where('kind', QuizKind::Content->value);
    }

    public function timestampedQuizzes()
    {
        return $this->hasMany(Quiz::class)->where('kind', QuizKind::Timestamped->value)->orderBy('timestamp_seconds');
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    public function speedLogs()
    {
        return $this->hasMany(SpeedLog::class);
    }
}
