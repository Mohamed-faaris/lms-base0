<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\NotificationStatus;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => NotificationStatus::class,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
