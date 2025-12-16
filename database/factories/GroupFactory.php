<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Space;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        return [
            'space_id' => Space::factory(),
            'name' => fake()->word(),
            'color' => fake()->hexColor(),
        ];
    }
}
