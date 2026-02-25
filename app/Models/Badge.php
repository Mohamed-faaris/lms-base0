<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = [
        'image',
        'title',
        'description',
        'conditions',
    ];

    protected function casts(): array
    {
        return [
            'conditions' => 'array',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'badge_assignments', 'badge_id', 'user_id')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }
}
