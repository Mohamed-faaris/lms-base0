<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseMeta extends Model
{
    protected $table = 'course_meta';

    protected $primaryKey = 'course_id';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'course_id',
        'category',
        'thumbnail',
        'difficulty',
        'duration',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
