<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\VideoEvent;

class SpeedLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'content_id',
        'event',
        'speed',
    ];

    protected function casts(): array
    {
        return [
            'event' => VideoEvent::class,
            'logged_at' => 'datetime',
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
