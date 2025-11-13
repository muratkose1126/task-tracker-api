<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\User;
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
            'title' => $this->faker->words(4, true),
            'description' => $this->faker->paragraph(),
            'status' => TaskStatus::TODO,
            'priority' => TaskPriority::MEDIUM,
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
        ];
    }

    /**
     * State for a task with TODO status.
     */
    public function todo(): static
    {
        return $this->state(['status' => TaskStatus::TODO]);
    }

    /**
     * State for a task with IN_PROGRESS status.
     */
    public function inProgress(): static
    {
        return $this->state(['status' => TaskStatus::IN_PROGRESS]);
    }

    /**
     * State for a task with DONE status.
     */
    public function done(): static
    {
        return $this->state(['status' => TaskStatus::DONE]);
    }

    /**
     * State for a high-priority task.
     */
    public function highPriority(): static
    {
        return $this->state(['priority' => TaskPriority::HIGH]);
    }

    /**
     * State for a low-priority task.
     */
    public function lowPriority(): static
    {
        return $this->state(['priority' => TaskPriority::LOW]);
    }

    /**
     * State for a task with urgent due date (tomorrow).
     */
    public function urgent(): static
    {
        return $this->state(['due_date' => now()->addDay()]);
    }

    /**
     * State for a task assigned to a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(['user_id' => $user->id]);
    }

    /**
     * State for a task in a specific project.
     */
    public function forProject(Project $project): static
    {
        return $this->state(['project_id' => $project->id]);
    }
}
