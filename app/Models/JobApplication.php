<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class JobApplication extends Model
{
    protected $fillable = [
        'job_posting_id',
        'full_name',
        'email',
        'phone',
        'location',
        'years_experience',
        'linkedin_url',
        'portfolio_url',
        'cover_letter',
        'resume_path',
        'status',
        'admin_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'years_experience' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public const STATUSES = [
        'pending' => 'Pending',
        'reviewing' => 'Reviewing',
        'shortlisted' => 'Shortlisted',
        'interviewing' => 'Interviewing',
        'rejected' => 'Rejected',
        'hired' => 'Hired',
    ];

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    /**
     * Public URL to the uploaded résumé (stored on the `public` disk).
     */
    public function getResumeUrlAttribute(): ?string
    {
        return $this->resume_path
            ? Storage::disk('public')->url($this->resume_path)
            : null;
    }
}
