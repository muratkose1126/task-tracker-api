<?php

use App\Models\User;

test('user can register with token auth', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    expect($response->status())->toBe(201);
    expect($response->json())->toHaveKeys(['token', 'user']);
});

test('user cannot register with duplicate email', function () {
    User::factory()->create(['email' => 'duplicate@example.com']);

    $response = $this->postJson('/api/auth/register', [
        'name' => 'Jane Doe',
        'email' => 'duplicate@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    expect($response->status())->toBe(422);
});

test('user cannot register with weak password', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    expect($response->status())->toBe(422);
});

test('user can login with token auth', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    expect($response->status())->toBe(200);
    expect($response->json())->toHaveKeys(['token', 'user']);
});

test('user cannot login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    expect($response->status())->toBe(422);
});

test('authenticated user can get profile', function () {
    $user = User::factory()->create();

    $response = $this->actingAsApi($user)
        ->getJson('/api/auth/me');

    expect($response->status())->toBe(200);
    expect($response->json('data.id'))->toBe($user->id);
});

test('unauthenticated user cannot get profile', function () {
    $response = $this->getJson('/api/auth/me');

    expect($response->status())->toBe(401);
});

test('user can logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/auth/logout');

    expect($response->status())->toBe(200);
});
