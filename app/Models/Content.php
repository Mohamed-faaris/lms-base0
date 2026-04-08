<?php

namespace App\Models;

use App\Enums\ContentType;
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
        return $this->hasOne(EndQuiz::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function timestampedQuizzes()
    {
        return $this->hasMany(TimestampedQuiz::class);
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
