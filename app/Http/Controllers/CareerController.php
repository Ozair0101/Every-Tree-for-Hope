<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobPosting;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    /**
     * Job board — listing with keyword search + filters.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $type = (string) $request->query('type', '');
        $category = (string) $request->query('category', '');

        $query = JobPosting::query()
            ->with(['company', 'category'])
            ->active()
            ->open()
            // Only show jobs whose company is active (or that have no company).
            ->where(function ($q) {
                $q->whereNull('company_id')
                    ->orWhereHas('company', fn ($c) => $c->where('is_active', true));
            });

        if ($search !== '') {
            // Search the translatable JSON title/summary, the location, and company name.
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

        $jobs = $query->ordered()->paginate(10)->withQueryString();

        // Category options limited to those that actually have open jobs.
        $categories = JobCategory::query()->active()->ordered()
            ->whereHas('jobs', fn ($q) => $q->active()->open())
            ->get();

        $totalOpen = JobPosting::query()->active()->open()
            ->where(function ($q) {
                $q->whereNull('company_id')
                    ->orWhereHas('company', fn ($c) => $c->where('is_active', true));
            })
            ->count();

        // AJAX: return only the results list (live search / filtering).
        if ($request->ajax() || $request->boolean('ajax')) {
            return view('partials.jobs-list', compact('jobs'));
        }

        return view('pages.careers', compact(
            'jobs',
            'search',
            'type',
            'category',
            'categories',
            'totalOpen',
        ));
    }

    /**
     * Single job detail + application form.
     */
    public function show(JobPosting $job)
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

        return view('pages.job-details', compact('job', 'relatedJobs'));
    }

    /**
     * Receive an application for a job.
     */
    public function apply(Request $request, JobPosting $job)
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

        $path = $request->file('resume')->store('job-applications/' . $job->id, 'public');

        JobApplication::create([
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

        return back()
            ->with('application_success', true)
            ->with('success', __('messages.jobs_apply_success'));
    }
}
