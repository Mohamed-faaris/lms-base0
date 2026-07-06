<?php

namespace App\Models;

use App\Enums\ModuleItemType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $course_module_id
 * @property int|null $content_asset_id
 * @property ModuleItemType $type
 * @property string $title
 * @property int $sort_order
 * @property bool $required
 * @property array|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class ModuleItem extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'type' => ModuleItemType::class,
            'required' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function courseModule(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class);
    }

    public function contentAsset(): BelongsTo
    {
        return $this->belongsTo(ContentAsset::class);
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }
}
