<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\MediaResource;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Gate;

class TaskAttachmentController extends Controller
{
    public function index(Task $task)
    {
        Gate::authorize('view', $task);

        $attachments = $task->getMedia('attachments');
        return MediaResource::collection($attachments);
    }

    public function store(Request $request, Task $task)
    {
        Gate::authorize('update', $task);

        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $media = $task
            ->addMediaFromRequest('file')
            ->toMediaCollection('attachments');

        return new MediaResource($media);
    }

    public function destroy(Media $attachment)
    {
        $task = $attachment->model;

        Gate::authorize('update', $task);

        $attachment->delete();

        return response()->noContent();
    }
}
