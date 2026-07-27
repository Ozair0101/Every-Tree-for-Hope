<?php

namespace App\Http\Controllers\Api;

use App\Models\TreeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON twin of \App\Http\Controllers\TreeRequestController.
 *
 * POST /api/tree-requests — ask for trees to be planted at a location
 *
 * Send as multipart/form-data. Photos and short videos go in `media[]`
 * (up to 10 files, 50 MB each).
 */
class TreeRequestController extends ApiController
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'location' => 'required|string|max:255',
            'number_of_trees' => 'required|integer|min:1|max:100000',
            'water_source' => 'nullable|string|max:255',
            'responsible_person' => 'required|string|max:255',
            'phone_whatsapp' => 'required|string|max:50',
            'media' => 'nullable|array|max:10',
            'media.*' => 'file|max:51200|mimetypes:image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime',
        ]);

        // Created first so uploads can be filed under the request's own id.
        $treeRequest = TreeRequest::create([
            'location' => $validated['location'],
            'number_of_trees' => $validated['number_of_trees'],
            'water_source' => $validated['water_source'] ?? null,
            'responsible_person' => $validated['responsible_person'],
            'phone_whatsapp' => $validated['phone_whatsapp'],
            'media_paths' => [],
        ]);

        $paths = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $paths[] = $file->store('tree-requests/'.$treeRequest->id, 'public');
            }
        }

        if (! empty($paths)) {
            $treeRequest->update(['media_paths' => $paths]);
        }

        return $this->created([
            'id' => $treeRequest->id,
            'media' => array_map(fn ($path) => asset('storage/'.$path), $paths),
        ], 'Your tree request has been submitted successfully. Our team will contact you soon.');
    }
}
