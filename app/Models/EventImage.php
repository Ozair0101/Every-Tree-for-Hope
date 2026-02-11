<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventImage extends Model
{
    protected $fillable = [
        'event_id',
        'image_path',
        'caption',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Get the event that owns the image
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the full image URL
     */
    public function getFullImageUrlAttribute()
    {
        if ($this->image_path) {
            // If it's already a full URL (starts with http), return as is
            if (str_starts_with($this->image_path, 'http')) {
                return $this->image_path;
            }
            // Otherwise, treat as stored file path
            return asset('storage/' . $this->image_path);
        }
        return asset('placeholder-event.jpg');
    }
}
