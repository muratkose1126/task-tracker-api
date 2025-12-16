<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkspaceMember>
 */
class WorkspaceMemberFactory extends Factory
{
    protected $model = WorkspaceMember::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'user_id' => User::factory(),
            'role' => 'member',
        ];
    }

    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    public function owner(): static
    {
        return $this->state(['role' => 'owner']);
    }

    public function guest(): static
    {
        return $this->state(['role' => 'guest']);
    }
}
