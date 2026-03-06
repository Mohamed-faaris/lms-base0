<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeAssignment extends Model
{
    protected $table = 'badge_assignments';

    public $incrementing = false;

    protected $primaryKey = ['user_id', 'badge_id'];

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'badge_id',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }
}
