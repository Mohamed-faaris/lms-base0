<?php

namespace App\Models;

use App\Enums\ProgressStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $enrollment_id
 * @property int $module_item_id
 * @property \App\Enums\ProgressStatus $status
 * @property float $progress
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property int $time_spent
 * @property int|null $score
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class LearningProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'module_item_id',
        'status',
        'progress',
        'started_at',
        'completed_at',
        'time_spent',
        'score',
    ];

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
