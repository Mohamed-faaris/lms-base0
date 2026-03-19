<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
    ];

    public function contents(): HasManyThrough
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

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledUsers()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'user_id')
            ->withPivot('enrolled_by', 'deadline', 'enrolled_at')
            ->withTimestamps();
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}
