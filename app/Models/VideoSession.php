<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $progress_id
 * @property int $last_second
 * @property int $watched_seconds
 * @property float $watch_percentage
 * @property int $seek_attempts
 * @property int $pause_count
 * @property float $playback_speed
 * @property int $focus_loss_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class VideoSession extends Model
{
    protected function casts(): array
    {
        return [
            'watch_percentage' => 'decimal:2',
            'playback_speed' => 'decimal:2',
        ];
    }

    public function progress(): BelongsTo
    {
        return $this->belongsTo(LearningProgress::class, 'progress_id');
    }
}
