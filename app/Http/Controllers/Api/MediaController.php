<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON twin of \App\Http\Controllers\MediaController — the video library.
 *
 * GET    /api/media         — active videos
 * POST   /api/media         — create
 * GET    /api/media/{media} — one video
 * PUT    /api/media/{media} — update
 * DELETE /api/media/{media} — delete
 *
 * The write endpoints mirror the web controller's CRUD. They are grouped
 * behind the `manage` prefix in routes/api.php so an auth middleware can be
 * added there in one place once the app has admin login.
 */
class MediaController extends ApiController
{
    /**
     * Active videos, newest first.
     */
    public function index(Request $request): JsonResponse
    {
        $media = Media::active()->ordered()->paginate($this->perPage($request, 12));

        return $this->paginated($media, MediaResource::class);
    }

    public function show(Media $media): JsonResponse
    {
        return $this->ok(new MediaResource($media));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'video_youtube_url' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $media = Media::create($validated);

        return $this->created(new MediaResource($media), 'Media item created successfully!');
    }

    public function update(Request $request, Media $media): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'video_youtube_url' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $media->update($validated);

        return $this->ok(new MediaResource($media->fresh()), 'Media item updated successfully!');
    }

    public function destroy(Media $media): JsonResponse
    {
        $media->delete();

        return $this->ok(null, 'Media item deleted successfully!');
    }
}
