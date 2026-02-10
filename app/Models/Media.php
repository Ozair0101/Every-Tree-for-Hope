<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'title',
        'date',
        'video_youtube_url',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get YouTube video ID from URL
     */
    public function getYoutubeVideoIdAttribute()
    {
        $url = $this->video_youtube_url;
        
        // Handle different YouTube URL formats
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        // If it's already just the ID, return it
        if (preg_match('/^[a-zA-Z0-9_-]+$/', $url)) {
            return $url;
        }
        
        return null;
    }

    /**
     * Get YouTube thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        $videoId = $this->youtube_video_id;
        return $videoId ? "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg" : null;
    }

    /**
     * Scope to get only active media
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order and date
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('date', 'desc');
    }
}
