<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $module_item_id
 * @property string $title
 * @property int $passing_marks
 * @property int|null $duration
 * @property int $attempt_limit
 * @property bool $shuffle_questions
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'shuffle_questions' => 'boolean',
        ];
    }

    public function moduleItem(): BelongsTo
    {
        return $this->belongsTo(ModuleItem::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
