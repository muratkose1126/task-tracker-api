<?php

namespace Database\Factories;

use App\Enums\TaskCommentType;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskComment>
 */
class TaskCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
            'comment' => $this->faker->sentence(),
            'type' => TaskCommentType::NOTE,
        ];
    }

    /**
     * State for a comment with a specific type.
     */
    public function ofType(TaskCommentType $type): static
    {
        return $this->state(['type' => $type]);
    }

    /**
     * State for a comment on a specific task.
     */
    public function forTask(Task $task): static
    {
        return $this->state(['task_id' => $task->id]);
    }

    /**
     * State for a comment by a specific user.
     */
    public function byUser(User $user): static
    {
        return $this->state(['user_id' => $user->id]);
    }

    /**
     * State for a detailed comment.
     */
    public function detailed(): static
    {
        return $this->state(['comment' => $this->faker->paragraph()]);
    }

    /**
     * State for an update type comment.
     */
    public function asUpdate(): static
    {
        return $this->state(['type' => TaskCommentType::UPDATE]);
    }

    /**
     * State for a reminder type comment.
     */
    public function asReminder(): static
    {
        return $this->state(['type' => TaskCommentType::REMINDER]);
    }
}
