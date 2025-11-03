<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Project;
use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'status' => TaskStatus::TODO,
            'priority' => TaskPriority::MEDIUM,
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
        ];
    }
}
