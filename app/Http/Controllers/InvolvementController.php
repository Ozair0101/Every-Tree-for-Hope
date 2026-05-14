<?php

namespace App\Http\Controllers;

use App\Models\InvolvementRequest;
use App\Models\UpcomingEvent;
use Illuminate\Http\Request;

class InvolvementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validateWithBag('involvement', [
            'type' => 'required|in:volunteer,sponsor,collaborate',
            'upcoming_event_id' => 'nullable|integer|exists:upcoming_events,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'message' => 'nullable|string|max:3000',
        ], [], [
            'type' => __('messages.involvement_type'),
            'name' => __('messages.your_name'),
            'email' => __('messages.email_address'),
            'phone' => __('messages.phone_number'),
            'message' => __('messages.message'),
        ]);

        // If the registration is tied to a specific upcoming event, prepend event details
        // to the message so admins see the context at a glance.
        if (!empty($validated['upcoming_event_id'])) {
            $event = UpcomingEvent::find($validated['upcoming_event_id']);
            if ($event) {
                $userNote = trim($validated['message'] ?? '');
                $validated['message'] = sprintf(
                    "[%s] %s — %s\n\n%s",
                    __('messages.future_admin_prefix'),
                    $event->title,
                    $event->date->format('M j, Y') . ' · ' . $event->location,
                    $userNote !== '' ? $userNote : '—'
                );
            }
        } else {
            // Message is required when not registering for a specific event
            $request->validateWithBag('involvement', [
                'message' => 'required|string|max:3000',
            ]);
        }

        $involvement = InvolvementRequest::create($validated);

        $typeLabels = [
            'volunteer' => __('messages.volunteer_request'),
            'sponsor' => __('messages.sponsor_request'),
            'collaborate' => __('messages.collaborate_request'),
        ];

        $successMessage = !empty($validated['upcoming_event_id'])
            ? __('messages.future_register_success')
            : __('messages.involvement_success', ['type' => $typeLabels[$validated['type']]]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
            ]);
        }

        return back()
            ->with('success', $successMessage)
            ->with('involvement_tab', $validated['type']);
    }
}
