<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    public $incrementing = false;
    protected $primaryKey = ['user_id', 'course_id'];

    protected $fillable = [
        'user_id',
        'enrolled_by',
        'course_id',
        'deadline',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enrolledBy()
    {
        return $this->belongsTo(User::class, 'enrolled_by');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
