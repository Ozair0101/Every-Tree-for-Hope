<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\FaqResource;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON twin of \App\Http\Controllers\FaqController.
 *
 * GET  /api/faqs — answered questions, grouped by category
 * POST /api/faqs — ask a new question (unverified until an admin answers)
 */
class FaqController extends ApiController
{
    /**
     * Answered FAQs. Returned both flat and grouped so the app can render
     * either a plain list or accordion sections without regrouping itself.
     */
    public function index(): JsonResponse
    {
        $faqs = Faq::verified()->ordered()->get();

        $groups = $faqs->groupBy('category')
            ->map(fn ($items, $category) => [
                'category' => $category,
                'faqs' => FaqResource::collection($items)->resolve(),
            ])
            ->values()
            ->all();

        return $this->ok([
            'faqs' => FaqResource::collection($faqs),
            'groups' => $groups,
            'count' => $faqs->count(),
        ]);
    }

    /**
     * Submit a question.
     *
     * Stored under 'en' (the canonical source locale) so admins see new
     * submissions whatever language the visitor was browsing in.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'question' => 'required|string|max:2000',
        ]);

        $faq = Faq::create([
            'question' => ['en' => $validated['question']],
            'asked_by_name' => $validated['name'] ?? null,
            'asked_by_email' => $validated['email'] ?? null,
            'category' => ['en' => 'General'],
            'is_verified' => false,
        ]);

        return $this->created(
            ['id' => $faq->id],
            'Your question has been submitted! We will review and answer it soon.'
        );
    }
}
