<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreTaskAttachmentRequest;
use App\Http\Resources\V1\MediaResource;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TaskAttachmentController extends Controller
{
    public function index(Task $task)
    {
        Gate::authorize('view', $task);

        $attachments = $task->getMedia('attachments');

        return MediaResource::collection($attachments);
    }

    public function store(StoreTaskAttachmentRequest $request, Task $task)
    {
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
