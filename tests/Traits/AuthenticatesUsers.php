<?php

namespace Tests\Traits;

use App\Models\User;

trait AuthenticatesUsers
{
    protected function actingAsUser(?User $user = null): User
    {
        $user ??= User::factory()->create();
        $this->actingAs($user, 'api');

        return $user;
    }

    protected function actingAsSessionUser(?User $user = null): User
    {
        $user ??= User::factory()->create();
        $this->actingAs($user, 'web');

        return $user;
    }

    protected function withApiToken(User $user): static
    {
        $token = $user->createToken('test-token')->plainTextToken;

        return $this->withHeader('Authorization', "Bearer {$token}");
    }

    protected function withoutApiToken(): static
    {
        return $this->withoutHeader('Authorization');
    }
}
