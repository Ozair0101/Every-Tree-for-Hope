<?php

namespace App\Http\Controllers;

use App\Models\InvolvementRequest;
use Illuminate\Http\Request;

class InvolvementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validateWithBag('involvement', [
            'type' => 'required|in:volunteer,sponsor,collaborate',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'message' => 'required|string|max:3000',
        ], [], [
            'type' => __('messages.involvement_type'),
            'name' => __('messages.your_name'),
            'email' => __('messages.email_address'),
            'phone' => __('messages.phone_number'),
            'message' => __('messages.message'),
        ]);

        $involvement = InvolvementRequest::create($validated);

        $typeLabels = [
            'volunteer' => __('messages.volunteer_request'),
            'sponsor' => __('messages.sponsor_request'),
            'collaborate' => __('messages.collaborate_request'),
        ];

        return back()
            ->with('success', __('messages.involvement_success', ['type' => $typeLabels[$validated['type']]]))
            ->with('involvement_tab', $validated['type']);
    }
}
