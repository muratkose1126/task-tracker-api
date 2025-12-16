<?php

namespace Database\Factories;

use App\Models\Space;
use App\Models\SpaceMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SpaceMember>
 */
class SpaceMemberFactory extends Factory
{
    protected $model = SpaceMember::class;

    public function definition(): array
    {
        return [
            'space_id' => Space::factory(),
            'user_id' => User::factory(),
            'role' => 'editor',
        ];
    }

    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    public function editor(): static
    {
        return $this->state(['role' => 'editor']);
    }

    public function commenter(): static
    {
        return $this->state(['role' => 'commenter']);
    }

    public function viewer(): static
    {
        return $this->state(['role' => 'viewer']);
    }
}
