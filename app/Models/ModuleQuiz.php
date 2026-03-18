<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleQuiz extends Model
{
    protected $table = 'module_quiz';

    protected $fillable = [
        'module_id',
        'quiz_id',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
