<?php

namespace App\Http\Controllers;

use App\Models\TreeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TreeRequestController extends Controller
{
    public function submit(Request $request)
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
                $paths[] = $file->store('tree-requests/' . $treeRequest->id, 'public');
            }
        }

        if (!empty($paths)) {
            $treeRequest->update(['media_paths' => $paths]);
        }

        return back()->with('success', 'Your tree request has been submitted successfully. Our team will contact you soon.');
    }
}
