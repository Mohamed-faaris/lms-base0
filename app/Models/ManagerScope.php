<?php

namespace App\Models;

use App\Enums\College;
use App\Enums\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagerScope extends Model
{
    /** @use HasFactory<\Database\Factories\ManagerScopeFactory> */
    use HasFactory;

    protected $fillable = [
        'manager_user_id',
        'college',
        'department',
    ];

    protected function casts(): array
    {
        return [
            'college' => College::class,
            'department' => Department::class,
        ];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function label(): string
    {
        $collegeLabel = $this->college?->label() ?? 'Unknown College';
        $departmentLabel = $this->department?->label();

        return $departmentLabel ? "{$collegeLabel} - {$departmentLabel}" : "{$collegeLabel} - All Departments";
    }
}
