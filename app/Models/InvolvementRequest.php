<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvolvementRequest extends Model
{
    protected $fillable = [
        'type',
        'upcoming_event_id',
        'name',
        'email',
        'phone',
        'message',
        'status',
        'admin_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function upcomingEvent()
    {
        return $this->belongsTo(UpcomingEvent::class);
    }

    public const TYPES = [
        'volunteer' => 'Volunteer',
        'sponsor' => 'Sponsor',
        'collaborate' => 'Collaborate',
    ];

    public const STATUSES = [
        'pending' => 'Pending',
        'reviewing' => 'Under Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'completed' => 'Completed',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function scopeVolunteer($query)
    {
        return $query->where('type', 'volunteer');
    }

    public function scopeSponsor($query)
    {
        return $query->where('type', 'sponsor');
    }

    public function scopeCollaborate($query)
    {
        return $query->where('type', 'collaborate');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
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
