<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

it('can list all attachments of a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $task->addMedia(UploadedFile::fake()->create('file1.pdf'))->toMediaCollection('attachments');
    $task->addMedia(UploadedFile::fake()->create('file2.pdf'))->toMediaCollection('attachments');

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/tasks/{$task->id}/attachments");

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

it('can upload an attachment to a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $file = UploadedFile::fake()->create('document.pdf', 500);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/tasks/{$task->id}/attachments", [
            'file' => $file,
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'file_name',
                'mime_type',
                'size',
                'url'
            ]
        ]);

    $this->assertDatabaseHas('media', [
        'model_id' => $task->id,
        'collection_name' => 'attachments',
        'file_name' => $file->getClientOriginalName(),
    ]);
});

it('can delete an attachment from a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $media = $task->addMedia(UploadedFile::fake()->create('delete-me.pdf'))->toMediaCollection('attachments');

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/attachments/{$media->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('media', ['id' => $media->id]);
});

it('prevents other users from managing attachments', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $owner->id]);

    $file = UploadedFile::fake()->create('file.pdf');

    $this->actingAs($other, 'sanctum')
        ->postJson("/api/v1/tasks/{$task->id}/attachments", ['file' => $file])
        ->assertStatus(403);

    $media = $task->addMedia($file)->toMediaCollection('attachments');

    $this->actingAs($other, 'sanctum')
        ->deleteJson("/api/v1/attachments/{$media->id}")
        ->assertStatus(403);
});
