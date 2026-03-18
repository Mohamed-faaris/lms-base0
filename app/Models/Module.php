<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'topic_id',
        'title',
        'description',
        'order',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }

    public function moduleQuizzes()
    {
        return $this->hasMany(ModuleQuiz::class);
    }
}
