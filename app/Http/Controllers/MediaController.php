<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $media = Media::active()->ordered()->paginate(12);
        return view('media.index', compact('media'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('media.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'video_youtube_url' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        Media::create($validated);

        return redirect()->route('media.index')
            ->with('success', 'Media item created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media)
    {
        return view('media.show', compact('media'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Media $media)
    {
        return view('media.edit', compact('media'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Media $media)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'video_youtube_url' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $media->update($validated);

        return redirect()->route('media.index')
            ->with('success', 'Media item updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media)
    {
        $media->delete();

        return redirect()->route('media.index')
            ->with('success', 'Media item deleted successfully!');
    }

    /**
     * API endpoint to get active media items for home page
     */
    public function apiIndex()
    {
        $media = Media::active()->ordered()->get();
        
        return response()->json([
            'media' => $media->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'date' => $item->date->format('M d, Y'),
                    'video_id' => $item->youtube_video_id,
                    'thumbnail_url' => $item->thumbnail_url,
                    'description' => $item->description,
                ];
            })
        ]);
    }
}
