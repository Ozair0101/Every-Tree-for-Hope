<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * One photograph on a maintenance visit.
 *
 * Stored as a relative path on the public disk; `image_url` resolves it to a
 * full URL so the app can render it without knowing the storage layout.
 */
class MaintenanceVisitImage extends Model
{
    protected $fillable = [
        'maintenance_visit_id',
        'image_path',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    protected $appends = ['image_url'];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(MaintenanceVisit::class, 'maintenance_visit_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (blank($this->image_path)) {
            return null;
        }

        return str_starts_with($this->image_path, 'http')
            ? $this->image_path
            : Storage::disk('public')->url($this->image_path);
    }
}
