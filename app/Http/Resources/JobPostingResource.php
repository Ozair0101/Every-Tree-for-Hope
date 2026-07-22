<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Job postings are translatable — every text field below already resolves
 * to the request locale (see SetApiLocale).
 *
 * The long-form fields are also exposed pre-split into lines so the mobile
 * app can render bullet lists without parsing text itself, which is what
 * the Blade views do via `$job->lines(...)`.
 *
 * @mixin \App\Models\JobPosting
 */
class JobPostingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'summary' => $this->summary,
            'description' => $this->description,

            'responsibilities' => $this->responsibilities,
            'requirements' => $this->requirements,
            'benefits' => $this->benefits,
            'responsibility_lines' => $this->lines('responsibilities'),
            'requirement_lines' => $this->lines('requirements'),
            'benefit_lines' => $this->lines('benefits'),

            'department' => $this->department,
            'employment_type' => $this->employment_type,
            'employment_type_label' => $this->employment_type_label,
            'experience_level' => $this->experience_level,
            'experience_level_label' => $this->experience_level_label,

            'location' => $this->location,
            'province' => $this->province,
            'is_remote' => (bool) $this->is_remote,
            'salary_range' => $this->salary_range,
            'application_url' => $this->application_url,
            'openings' => $this->openings,
            'application_deadline' => $this->application_deadline?->toDateString(),

            'is_open' => $this->is_open,
            'is_featured' => (bool) $this->is_featured,

            'company' => new CompanyResource($this->whenLoaded('company')),
            'category' => new JobCategoryResource($this->whenLoaded('category')),

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
