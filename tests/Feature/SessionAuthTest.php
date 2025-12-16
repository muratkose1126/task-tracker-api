<?php

use App\Models\User;

test('user can register with session auth', function () {
    $response = $this->postJson('/api/auth/session/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    expect($response->status())->toBe(201);
    expect($response->json())->toHaveKeys(['user', 'message']);
});

test('user can login with session auth', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/session/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    expect($response->status())->toBe(200);
    expect($response->json())->toHaveKeys(['user', 'message']);
});

test('login fails with invalid credentials', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/session/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    expect($response->status())->toBe(422);
});

test('authenticated user can get profile', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'web')
        ->getJson('/api/auth/session/me');

    expect($response->status())->toBe(200);
    expect($response->json())->toHaveKey('data');
});

test('unauthenticated user cannot get profile', function () {
    $response = $this->getJson('/api/auth/session/me');

    expect($response->status())->toBe(401);
});

test('user can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'web')
        ->postJson('/api/auth/session/logout');

    expect($response->status())->toBe(200);
    expect($response->json())->toHaveKey('message');
});

test('register requires valid data', function () {
    $response = $this->postJson('/api/auth/session/register', [
        'name' => '',
        'email' => 'invalid-email',
        'password' => '123',
    ]);

    expect($response->status())->toBe(422);
});

test('cannot register with existing email', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->postJson('/api/auth/session/register', [
        'name' => 'New User',
        'email' => 'existing@example.com',
        'password' => 'password123',
    ]);

    expect($response->status())->toBe(422);
});
