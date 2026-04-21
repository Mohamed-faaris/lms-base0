<?php

namespace Database\Factories;

use App\Enums\College;
use App\Enums\Department;
use App\Models\ManagerScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ManagerScope>
 */
class ManagerScopeFactory extends Factory
{
    protected $model = ManagerScope::class;

    public function definition(): array
    {
        return [
            'manager_user_id' => User::factory()->manager(),
            'college' => fake()->randomElement(College::cases()),
            'department' => fake()->boolean(70) ? fake()->randomElement(Department::cases()) : null,
        ];
    }

    public function collegeWide(?College $college = null): static
    {
        return $this->state(fn () => [
            'college' => $college ?? fake()->randomElement(College::cases()),
            'department' => null,
        ]);
    }
}
