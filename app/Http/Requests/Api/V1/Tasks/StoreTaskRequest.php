<?php

namespace App\Http\Requests\Api\V1\Tasks;

use App\Enums\TaskPriority;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Creating a task.
 *
 * Authorisation is the policy's job, not a boolean here — `authorize()` defers
 * to `TaskPolicy::create()` so the rule lives in one place and the admin panel
 * gets the same answer.
 */
class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Task::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:5000'],
            'instructions' => ['nullable', 'string', 'max:20000'],

            'priority' => ['sometimes', Rule::enum(TaskPriority::class)],
            'task_category_id' => ['nullable', 'integer', 'exists:task_categories,id'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'upcoming_event_id' => ['nullable', 'integer', 'exists:upcoming_events,id'],
            'parent_task_id' => ['nullable', 'integer', 'exists:tasks,id'],

            'start_date' => ['nullable', 'date'],
            // A deadline before the start date is almost always a typo, and one
            // that would make the task overdue the moment it is published.
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],

            'estimated_hours' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],

            // Geofence: all or nothing. A latitude with no radius cannot be
            // checked against, and would silently disable the geo verification
            // the task claims to require.
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'radius' => ['nullable', 'integer', 'min:10', 'max:50000'],
            'location_name' => ['nullable', 'string', 'max:255'],

            'requires_photo' => ['sometimes', 'boolean'],
            'requires_geo_check' => ['sometimes', 'boolean'],
            'requires_review' => ['sometimes', 'boolean'],
            'max_assignees' => ['nullable', 'integer', 'min:1', 'max:500'],

            'checklist' => ['sometimes', 'array', 'max:50'],
            'checklist.*.title' => ['required', 'string', 'max:255'],
            'checklist.*.description' => ['nullable', 'string', 'max:1000'],
            'checklist.*.is_required' => ['sometimes', 'boolean'],
            'checklist.*.requires_photo' => ['sometimes', 'boolean'],

            'assignee_ids' => ['sometimes', 'array', 'max:100'],
            'assignee_ids.*' => ['integer', 'exists:users,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Requiring a geo check with nowhere to check against would reject
            // every submission the task ever receives.
            if ($this->boolean('requires_geo_check') && $this->input('latitude') === null) {
                $validator->errors()->add(
                    'requires_geo_check',
                    'A geo check needs a location: provide latitude and longitude, or turn the check off.',
                );
            }

            $max = $this->input('max_assignees');
            $count = count($this->input('assignee_ids', []));

            if ($max !== null && $count > (int) $max) {
                $validator->errors()->add(
                    'assignee_ids',
                    "This task allows at most {$max} assignees, but {$count} were given.",
                );
            }
        });
    }

    /** @return array<string, mixed> the columns, without the nested payloads. */
    public function taskAttributes(): array
    {
        return $this->safe()->except(['checklist', 'assignee_ids']);
    }
}
