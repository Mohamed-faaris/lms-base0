<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BehavioralEvent extends Model
{
    protected $fillable = [
        'user_id',
        'content_id',
        'course_id',
        'event_type',
        'duration_seconds',
        'video_timestamp',
        'pause_count',
        'seek_position',
        'metadata',
    ];

    protected $casts = [
        'event_timestamp' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
