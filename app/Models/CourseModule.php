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
 * @property int $course_version_id
 * @property string $title
 * @property string|null $description
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class CourseModule extends Model
{
    use HasFactory, SoftDeletes;

    public function courseVersion(): BelongsTo
    {
        return $this->belongsTo(CourseVersion::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModuleItem::class)->orderBy('sort_order');
    }
}
