<?php

namespace Database\Factories;

use App\Models\Space;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Space>
 */
class SpaceFactory extends Factory
{
    protected $model = Space::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'name' => fake()->word(),
            'visibility' => 'public',
            'color' => fake()->hexColor(),
            'is_archived' => false,
        ];
    }

    public function private(): static
    {
        return $this->state(['visibility' => 'private']);
    }

    public function public(): static
    {
        return $this->state(['visibility' => 'public']);
    }

    public function archived(): static
    {
        return $this->state(['is_archived' => true]);
    }
}
