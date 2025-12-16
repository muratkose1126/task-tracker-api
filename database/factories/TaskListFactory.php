<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Space;
use App\Models\TaskList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskList>
 */
class TaskListFactory extends Factory
{
    protected $model = TaskList::class;

    public function definition(): array
    {
        return [
            'space_id' => Space::factory(),
            'group_id' => null,
            'name' => fake()->word(),
            'status_schema' => null,
            'is_archived' => false,
        ];
    }

    public function withGroup(): static
    {
        return $this->state([
            'group_id' => Group::factory(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(['is_archived' => true]);
    }

    public function withStatusSchema(): static
    {
        return $this->state([
            'status_schema' => ['todo', 'in_progress', 'done'],
        ]);
    }
}
