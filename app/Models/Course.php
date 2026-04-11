<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Course extends Model implements HasMedia
{
    use HasSlug;
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'description',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('course-thumbnail')
            ->singleFile()
            ->useDisk(config('media-library.disk_name', 'public'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(960)
            ->height(540)
            ->sharpen(10)
            ->nonQueued();
    }

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
}
