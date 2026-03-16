<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'college_id',
        'department_name',
        'department_code',
        'hod_name',
        'status',
    ];

    protected $casts = [
        'college_id' => 'integer',
        'status' => 'string',
    ];

    /**
     * Get the college that owns this department.
     */
    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }

    /**
     * Get all faculties for this department.
     */
    public function faculties(): HasMany
    {
        return $this->hasMany(Faculty::class);
    }
}
