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
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get YouTube video ID from URL (supports regular videos, Shorts, youtu.be, embed)
     */
    public function getYoutubeVideoIdAttribute()
    {
        $url = $this->video_youtube_url;

        if (!$url) return null;

        // youtube.com/shorts/ID
        if (preg_match('/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        // youtube.com/watch?v=ID
        if (preg_match('/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        // youtu.be/ID
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        // youtube.com/embed/ID
        if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        // Plain ID (11-char alphanumeric)
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        return null;
    }

    /**
     * Detect if the stored URL is a YouTube Short
     */
    public function getIsShortAttribute(): bool
    {
        return $this->video_youtube_url
            ? str_contains($this->video_youtube_url, '/shorts/')
            : false;
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
     * Scope to order by date (newest first)
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('date', 'desc');
    }
}
