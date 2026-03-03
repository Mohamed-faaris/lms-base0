<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Xp extends Model
{
    public $table = 'xp';

    public $incrementing = false;

    protected $primaryKey = 'user_id';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'xp',
    ];

    protected function casts(): array
    {
        return [
            'updated_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
