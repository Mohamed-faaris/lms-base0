<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Course extends Model
{
    use HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'description',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function contents()
    {
        return $this->hasManyThrough(Content::class, Module::class);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function courseMeta()
    {
        return $this->hasOne(CourseMeta::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledUsers()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'user_id')
            ->withPivot('enrolled_by', 'deadline', 'enrolled_at')
            ->withTimestamps();
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function quizzes()
    {
        return $this->hasManyThrough(Quiz::class, [Module::class, Content::class]);
    }

    public function endQuizzes()
    {
        return $this->hasManyThrough(EndQuiz::class, [Module::class, Content::class]);
    }
}
