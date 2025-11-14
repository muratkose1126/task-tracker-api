<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can register with session auth', function () {
    $response = $this->postJson('/api/auth/session/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'message',
        ]);

    $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    $this->assertAuthenticated();
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

    $response->assertStatus(200)
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'message',
        ]);

    $this->assertAuthenticated();
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

    $response->assertStatus(422)->assertJsonValidationErrors('email');
    $this->assertGuest();
});

test('authenticated user can get profile', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'web')
        ->getJson('/api/auth/session/me');

    $response->assertStatus(200)
        ->assertJsonStructure(['data' => ['id', 'name', 'email']]);
});

test('unauthenticated user cannot get profile', function () {
    $this->getJson('/api/auth/session/me')->assertStatus(401);
});

test('user can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'web')
        ->postJson('/api/auth/session/logout');

    $response->assertStatus(200)->assertJsonStructure(['message']);
    $this->assertGuest();
});

test('register requires valid data', function () {
    $response = $this->postJson('/api/auth/session/register', [
        'name' => '',
        'email' => 'invalid-email',
        'password' => '123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

test('cannot register with existing email', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->postJson('/api/auth/session/register', [
        'name' => 'New User',
        'email' => 'existing@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('email');
});
