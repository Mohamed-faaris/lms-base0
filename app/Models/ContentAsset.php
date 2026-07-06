<?php

namespace App\Models;

use App\Enums\StorageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $type
 * @property string $title
 * @property StorageType $storage
 * @property string|null $path
 * @property array|null $metadata
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class ContentAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'storage' => StorageType::class,
            'metadata' => 'array',
        ];
    }

    public function moduleItems(): HasMany
    {
        return $this->hasMany(ModuleItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
