<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\JobCategoryResource;
use App\Http\Resources\JobPostingResource;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobPosting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON twin of \App\Http\Controllers\CareerController.
 *
 * GET  /api/careers               — job board, searchable + filterable
 * GET  /api/careers/filters       — filter options for the search screen
 * GET  /api/careers/{job}         — one job (by slug) + related jobs
 * POST /api/careers/{job}/apply   — submit an application (multipart)
 */
class CareerController extends ApiController
{
    /**
     * Open positions.
     *
     * Query parameters: `q` (keyword), `type` (employment type key),
     * `category` (category slug), `per_page`.
     */
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('q', ''));
        $type = (string) $request->query('type', '');
        $category = (string) $request->query('category', '');

        $query = JobPosting::query()
            ->with(['company', 'category'])
            ->active()
            ->open()
            // Hide jobs belonging to a deactivated company.
            ->where(function ($q) {
                $q->whereNull('company_id')
                    ->orWhereHas('company', fn ($c) => $c->where('is_active', true));
            });

        if ($search !== '') {
            // Searches the translatable JSON title/summary, the location and
            // the company name.
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('company', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if ($type !== '' && \array_key_exists($type, JobPosting::EMPLOYMENT_TYPES)) {
            $query->where('employment_type', $type);
        }

        if ($category !== '') {
            $query->whereHas('category', fn ($c) => $c->where('slug', $category));
        }

        $jobs = $query->ordered()
            ->paginate($this->perPage($request, 10))
            ->withQueryString();

        return $this->paginated($jobs, JobPostingResource::class, [
            'filters' => [
                'q' => $search,
                'type' => $type,
                'category' => $category,
            ],
            'total_open' => $this->totalOpenCount(),
        ]);
    }

    /**
     * Everything the app needs to build its filter UI: the categories that
     * actually have open jobs, plus the fixed type/level dictionaries.
     */
    public function filters(): JsonResponse
    {
        $categories = JobCategory::query()->active()->ordered()
            ->whereHas('jobs', fn ($q) => $q->active()->open())
            ->get();

        return $this->ok([
            'categories' => JobCategoryResource::collection($categories),
            'employment_types' => $this->asOptions(JobPosting::EMPLOYMENT_TYPES),
            'experience_levels' => $this->asOptions(JobPosting::EXPERIENCE_LEVELS),
            'departments' => array_values(JobPosting::DEPARTMENTS),
            'total_open' => $this->totalOpenCount(),
        ]);
    }

    /**
     * One job posting, resolved by slug, plus up to three related jobs.
     */
    public function show(JobPosting $job): JsonResponse
    {
        abort_unless($job->is_active, 404);

        $job->load(['company', 'category']);

        $relatedJobs = JobPosting::query()
            ->with(['company', 'category'])
            ->active()->open()
            ->where('id', '!=', $job->id)
            ->when($job->job_category_id, fn ($q) => $q->where('job_category_id', $job->job_category_id))
            ->ordered()
            ->take(3)
            ->get();

        return $this->ok([
            'job' => new JobPostingResource($job),
            'related_jobs' => JobPostingResource::collection($relatedJobs),
        ]);
    }

    /**
     * Receive an application. Send as multipart/form-data — `resume` is a
     * required PDF/DOC/DOCX file of up to 10 MB.
     */
    public function apply(Request $request, JobPosting $job): JsonResponse
    {
        abort_unless($job->is_open, 404);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'location' => 'nullable|string|max:255',
            'years_experience' => 'nullable|integer|min:0|max:80',
            'linkedin_url' => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
            'cover_letter' => 'nullable|string|max:5000',
            'resume' => 'required|file|max:10240|mimes:pdf,doc,docx',
        ], [
            'resume.required' => __('messages.jobs_resume_required'),
            'resume.mimes' => __('messages.jobs_resume_mimes'),
            'resume.max' => __('messages.jobs_resume_max'),
        ]);

        $path = $request->file('resume')->store('job-applications/'.$job->id, 'public');

        $application = JobApplication::create([
            'job_posting_id' => $job->id,
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'location' => $validated['location'] ?? null,
            'years_experience' => $validated['years_experience'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'resume_path' => $path,
            'status' => 'pending',
        ]);

        return $this->created([
            'application_id' => $application->id,
            'status' => $application->status,
        ], __('messages.jobs_apply_success'));
    }

    /**
     * Open positions at active companies — the number shown on the board.
     */
    protected function totalOpenCount(): int
    {
        return JobPosting::query()->active()->open()
            ->where(function ($q) {
                $q->whereNull('company_id')
                    ->orWhereHas('company', fn ($c) => $c->where('is_active', true));
            })
            ->count();
    }

    /**
     * Turn a `key => label` constant into a list the app can map over.
     *
     * @param  array<string, string>  $map
     */
    protected function asOptions(array $map): array
    {
        return collect($map)
            ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();
    }
}
