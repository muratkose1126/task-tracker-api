<?php

namespace Database\Factories;

use App\Enums\ProjectRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectMember>
 */
class ProjectMemberFactory extends Factory
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
            'role' => ProjectRole::DEVELOPER,
        ];
    }

    /**
     * State for an owner member.
     */
    public function owner(): static
    {
        return $this->state(['role' => ProjectRole::OWNER]);
    }

    /**
     * State for a manager member.
     */
    public function manager(): static
    {
        return $this->state(['role' => ProjectRole::MANAGER]);
    }

    /**
     * State for a developer member.
     */
    public function developer(): static
    {
        return $this->state(['role' => ProjectRole::DEVELOPER]);
    }

    /**
     * State for a tester member.
     */
    public function tester(): static
    {
        return $this->state(['role' => ProjectRole::TESTER]);
    }

    /**
     * State for a viewer member.
     */
    public function viewer(): static
    {
        return $this->state(['role' => ProjectRole::VIEWER]);
    }

    /**
     * State for a member in a specific project.
     */
    public function forProject(Project $project): static
    {
        return $this->state(['project_id' => $project->id]);
    }

    /**
     * State for a specific user as member.
     */
    public function forUser(User $user): static
    {
        return $this->state(['user_id' => $user->id]);
    }
}
