<?php

namespace App\Models;

use App\Enums\ContentType;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'course_id',
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

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function endQuiz()
    {
        return $this->hasMany(EndQuiz::class);
    }

    public function modelQuiz()
    {
        return $this->hasMany(ModelQuiz::class);
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
