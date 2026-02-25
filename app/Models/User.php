<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\Role;
use App\Enums\College;
use App\Enums\Department;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'college',
        'department',
        'role',
        'image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'college' => College::class,
            'department' => Department::class,
        ];
    }

    public function meta()
    {
        return $this->hasOne(UserMeta::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'user_id', 'course_id')
            ->withPivot('enrolled_by', 'deadline', 'enrolled_at')
            ->withTimestamps();
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function speedLogs()
    {
        return $this->hasMany(SpeedLog::class);
    }

    public function streaks()
    {
        return $this->hasMany(Streak::class);
    }

    public function xp()
    {
        return $this->hasOne(Xp::class);
    }

    public function xpLogs()
    {
        return $this->hasMany(XpLog::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'badge_assignments', 'user_id', 'badge_id')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
