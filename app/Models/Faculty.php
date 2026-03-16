<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'college_id',
        'department_id',
        'faculty_name',
        'faculty_email',
        'phone',
        'designation',
        'status',
    ];

    protected $casts = [
        'college_id' => 'integer',
        'department_id' => 'integer',
        'status' => 'string',
    ];

    /**
     * Get the college that owns this faculty.
     */
    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }

    /**
     * Get the department that owns this faculty.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
