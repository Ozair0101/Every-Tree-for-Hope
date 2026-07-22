<?php

namespace App\Http\Controllers\Api;

use App\Models\InvolvementRequest;
use App\Models\UpcomingEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON twin of \App\Http\Controllers\InvolvementController.
 *
 * POST /api/involvement — volunteer, sponsor or collaborate
 *
 * Send as multipart/form-data when `type=volunteer`, since that branch
 * requires a `cv` PDF. Pass `upcoming_event_id` to register for a specific
 * event instead of making a general enquiry.
 */
class InvolvementController extends ApiController
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:volunteer,sponsor,collaborate',
            'upcoming_event_id' => 'nullable|integer|exists:upcoming_events,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            // Required only for a general enquiry — see the check below.
            'message' => 'nullable|string|max:3000',
            // Volunteer-only fields.
            'experience' => 'nullable|required_if:type,volunteer|string|max:3000',
            'province' => 'nullable|required_if:type,volunteer|string|max:120',
            'home_address' => 'nullable|required_if:type,volunteer|string|max:255',
            'leisure_time' => 'nullable|required_if:type,volunteer|string|max:255',
            'current_job' => 'nullable|required_if:type,volunteer|string|max:255',
            'cv' => 'nullable|required_if:type,volunteer|file|mimes:pdf|max:5120',
        ], [], [
            'type' => __('messages.involvement_type'),
            'name' => __('messages.your_name'),
            'email' => __('messages.email_address'),
            'phone' => __('messages.phone_number'),
            'message' => __('messages.message'),
            'experience' => __('messages.vol_experience'),
            'province' => __('messages.vol_province'),
            'home_address' => __('messages.vol_home_address'),
            'leisure_time' => __('messages.vol_leisure_time'),
            'current_job' => __('messages.vol_current_job'),
            'cv' => __('messages.vol_cv'),
        ]);

        // A message is only optional when registering for a known event —
        // otherwise we have no idea what the person is asking for.
        if (empty($validated['upcoming_event_id'])) {
            $request->validate(['message' => 'required|string|max:3000']);
        }

        // Store the CV on the public disk; only the path is a column.
        if ($request->hasFile('cv')) {
            $validated['cv_path'] = $request->file('cv')->store('volunteer-cvs', 'public');
        }
        unset($validated['cv']);

        // When tied to an event, prepend its details so admins get the
        // context at a glance — same format the web form produces.
        if (! empty($validated['upcoming_event_id'])) {
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
        }

        $involvement = InvolvementRequest::create($validated);

        $typeLabels = [
            'volunteer' => __('messages.volunteer_request'),
            'sponsor' => __('messages.sponsor_request'),
            'collaborate' => __('messages.collaborate_request'),
        ];

        $successMessage = ! empty($validated['upcoming_event_id'])
            ? __('messages.future_register_success')
            : __('messages.involvement_success', ['type' => $typeLabels[$validated['type']]]);

        return $this->created([
            'id' => $involvement->id,
            'type' => $involvement->type,
        ], $successMessage);
    }
}
