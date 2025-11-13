<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
        ];
    }

    /**
     * State for a project with a specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(['name' => $name]);
    }

    /**
     * State for a complete project with detailed description.
     */
    public function detailed(): static
    {
        return $this->state([
            'description' => $this->faker->paragraphs(3, true),
        ]);
    }
}
