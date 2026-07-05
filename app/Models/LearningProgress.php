<?php

namespace App\Models;

use App\Enums\ProgressStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LearningProgress extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => ProgressStatus::class,
            'progress' => 'decimal:2',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollment::class, 'enrollment_id');
    }

    public function moduleItem(): BelongsTo
    {
        return $this->belongsTo(ModuleItem::class);
    }

    public function videoSession(): HasOne
    {
        return $this->hasOne(VideoSession::class, 'progress_id');
    }
}
