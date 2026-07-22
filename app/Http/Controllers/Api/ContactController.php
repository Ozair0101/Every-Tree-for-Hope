<?php

namespace App\Http\Controllers\Api;

use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON twin of \App\Http\Controllers\ContactController.
 *
 * POST /api/contact — send a message to the team
 */
class ContactController extends ApiController
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $message = ContactMessage::create($validated);

        return $this->created(
            ['id' => $message->id],
            'Your message has been sent successfully! We will get back to you soon.'
        );
    }
}
