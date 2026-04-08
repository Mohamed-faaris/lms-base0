<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    public $incrementing = false;

    protected $primaryKey = ['user_id', 'content_id'];

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'content_id',
        'progress_seconds',
        'video_duration',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'last_watched_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}
