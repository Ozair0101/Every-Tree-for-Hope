<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::verified()->ordered()->get()->groupBy('category');
        $faqCount = Faq::verified()->count();

        return view('pages.faq', compact('faqs', 'faqCount'));
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'question' => 'required|string|max:2000',
        ]);

        Faq::create([
            'question' => $validated['question'],
            'asked_by_name' => $validated['name'] ?? null,
            'asked_by_email' => $validated['email'] ?? null,
            'category' => 'General',
            'is_verified' => false,
        ]);

        return redirect()->route('faq')->with('success', 'Your question has been submitted! We will review and answer it soon.');
    }
}
