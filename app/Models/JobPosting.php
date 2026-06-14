<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class JobPosting extends Model
{
    use HasTranslations;

    /**
     * Stored as {"en": "...", "fa": "...", "ps": "..."} JSON.
     * Reading the attribute returns the current locale, falling back to English.
     */
    public array $translatable = [
        'title',
        'summary',
        'description',
        'responsibilities',
        'requirements',
        'benefits',
    ];

    protected $fillable = [
        'company_id',
        'job_category_id',
        'slug',
        'title',
        'summary',
        'description',
        'responsibilities',
        'requirements',
        'benefits',
        'department',
        'employment_type',
        'experience_level',
        'location',
        'province',
        'is_remote',
        'salary_range',
        'application_url',
        'openings',
        'application_deadline',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'application_deadline' => 'date',
        'is_remote' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'openings' => 'integer',
        'sort_order' => 'integer',
    ];

    public const EMPLOYMENT_TYPES = [
        'full_time' => 'Full-time',
        'part_time' => 'Part-time',
        'contract' => 'Contract',
        'temporary' => 'Temporary',
        'internship' => 'Internship',
        'volunteer' => 'Volunteer',
    ];

    public const EXPERIENCE_LEVELS = [
        'entry' => 'Entry level',
        'mid' => 'Mid level',
        'senior' => 'Senior level',
        'lead' => 'Lead / Manager',
    ];

    public const DEPARTMENTS = [
        'Field Operations' => 'Field Operations',
        'Nursery & Forestry' => 'Nursery & Forestry',
        'Programs' => 'Programs',
        'Administration' => 'Administration',
        'Finance' => 'Finance',
        'Communications & Media' => 'Communications & Media',
        'Fundraising' => 'Fundraising',
        'Volunteer Coordination' => 'Volunteer Coordination',
        'Monitoring & Evaluation' => 'Monitoring & Evaluation',
        'IT & Digital' => 'IT & Digital',
    ];

    protected static function booted(): void
    {
        static::creating(function (JobPosting $job) {
            if (empty($job->slug)) {
                $job->slug = static::uniqueSlug((string) $job->getTranslation('title', 'en', false));
            }
        });
    }

    /**
     * Build a URL-safe, unique slug from a source string.
     */
    public static function uniqueSlug(string $source): string
    {
        $base = Str::slug($source) ?: 'job';
        $slug = $base;
        $i = 2;

        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Not past its application deadline (open deadline = always open).
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('application_deadline')
                ->orWhereDate('application_deadline', '>=', now()->toDateString());
        });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('created_at');
    }

    public function getEmploymentTypeLabelAttribute(): string
    {
        return self::EMPLOYMENT_TYPES[$this->employment_type] ?? Str::headline((string) $this->employment_type);
    }

    public function getExperienceLevelLabelAttribute(): ?string
    {
        if (! $this->experience_level) {
            return null;
        }

        return self::EXPERIENCE_LEVELS[$this->experience_level] ?? Str::headline($this->experience_level);
    }

    /**
     * Whether the posting still accepts applications.
     */
    public function getIsOpenAttribute(): bool
    {
        return $this->is_active
            && (! $this->application_deadline || ! $this->application_deadline->isPast());
    }

    /**
     * Split a translatable multi-line field into trimmed, non-empty lines
     * so the view can render them as a bullet list.
     */
    public function lines(string $attribute): array
    {
        $value = (string) ($this->{$attribute} ?? '');

        return collect(preg_split('/\r\n|\r|\n/', $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
