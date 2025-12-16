<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('user can upload attachment to task', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/tasks/{$task->id}/attachments", [
            'file' => $file,
        ]);

    expect($response->status())->toBe(201);
    expect($task->fresh()->media)->toHaveCount(1);
});

test('user can list attachments', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $file1 = UploadedFile::fake()->create('file1.pdf');
    $file2 = UploadedFile::fake()->create('file2.jpg');

    $this->actingAsApi($user)
        ->postJson("/api/v1/tasks/{$task->id}/attachments", ['file' => $file1]);

    $this->actingAsApi($user)
        ->postJson("/api/v1/tasks/{$task->id}/attachments", ['file' => $file2]);

    $response = $this->actingAsApi($user)
        ->getJson("/api/v1/tasks/{$task->id}/attachments");

    expect($response->status())->toBe(200);
    expect($response->json())->toHaveKey('data');
});

test('user can delete attachment', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $file = UploadedFile::fake()->create('document.pdf');

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/tasks/{$task->id}/attachments", ['file' => $file]);

    $mediaId = $response->json('data.id');

    $deleteResponse = $this->actingAsApi($user)
        ->deleteJson("/api/v1/attachments/{$mediaId}");

    expect($deleteResponse->status())->toBe(204);
    expect($task->fresh()->media)->toHaveCount(0);
});

test('file is required for upload', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/tasks/{$task->id}/attachments", []);

    expect($response->status())->toBe(422);
});

test('uploaded file must be file', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/tasks/{$task->id}/attachments", [
            'file' => 'not a file',
        ]);

    expect($response->status())->toBe(422);
});

test('unauthenticated user cannot upload', function () {
    $task = Task::factory()->create();

    $file = UploadedFile::fake()->create('document.pdf');

    $response = $this->postJson("/api/v1/tasks/{$task->id}/attachments", [
        'file' => $file,
    ]);

    expect($response->status())->toBe(401);
});

test('multiple files can be attached to task', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    for ($i = 0; $i < 3; $i++) {
        $file = UploadedFile::fake()->create("file{$i}.pdf");
        $this->actingAsApi($user)
            ->postJson("/api/v1/tasks/{$task->id}/attachments", ['file' => $file]);
    }

    expect($task->fresh()->media)->toHaveCount(3);
});

test('task without attachments returns empty list', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $response = $this->actingAsApi($user)
        ->getJson("/api/v1/tasks/{$task->id}/attachments");

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toHaveCount(0);
});
