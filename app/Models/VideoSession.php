<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
