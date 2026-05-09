<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreeRequest extends Model
{
    protected $fillable = [
        'location',
        'number_of_trees',
        'water_source',
        'responsible_person',
        'phone_whatsapp',
        'media_paths',
        'status',
        'admin_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'media_paths' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public const STATUSES = [
        'pending' => 'Pending',
        'reviewing' => 'Under Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'completed' => 'Completed',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function markAsReviewing(): void
    {
        $this->update([
            'status' => 'reviewing',
            'reviewed_at' => now(),
        ]);
    }

    public function markAsApproved(?string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'admin_notes' => $notes,
        ]);
    }

    public function markAsRejected(?string $notes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'admin_notes' => $notes,
        ]);
    }
}
