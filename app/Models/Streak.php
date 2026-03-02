<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Streak extends Model
{
    public $incrementing = false;
    protected $primaryKey = ['user_id', 'date'];

    protected $fillable = [
        'user_id',
        'count',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
