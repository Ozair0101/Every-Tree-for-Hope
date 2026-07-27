<?php

namespace App\Http\Requests\Api\V1\Tasks;

use App\Enums\TaskPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Editing a task's definition.
 *
 * Every field is `sometimes`, so this supports PATCH semantics — a client
 * sending only `{"due_date": ...}` must not blank the instructions. `status` is
 * absent by design: it moves only through the state machine's own endpoints,
 * never by assignment.
 */
class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('task')) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:180'],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'instructions' => ['sometimes', 'nullable', 'string', 'max:20000'],

            'priority' => ['sometimes', Rule::enum(TaskPriority::class)],
            'task_category_id' => ['sometimes', 'nullable', 'integer', 'exists:task_categories,id'],

            'start_date' => ['sometimes', 'nullable', 'date'],
            'due_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],
            'estimated_hours' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:9999.99'],

            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'radius' => ['sometimes', 'nullable', 'integer', 'min:10', 'max:50000'],
            'location_name' => ['sometimes', 'nullable', 'string', 'max:255'],

            'requires_photo' => ['sometimes', 'boolean'],
            'requires_geo_check' => ['sometimes', 'boolean'],
            'requires_review' => ['sometimes', 'boolean'],
            'max_assignees' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:500'],

            // Sending `checklist` replaces the list; omitting it leaves it alone.
            // An empty array is therefore a meaningful instruction — "no steps" —
            // and is accepted as such.
            'checklist' => ['sometimes', 'array', 'max:50'],
            'checklist.*.title' => ['required', 'string', 'max:255'],
            'checklist.*.description' => ['nullable', 'string', 'max:1000'],
            'checklist.*.is_required' => ['sometimes', 'boolean'],
            'checklist.*.requires_photo' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $task = $this->route('task');

            // Shrinking the cap below the number of people already on the task
            // would leave it permanently over its own limit.
            if ($this->filled('max_assignees') && $task) {
                $current = $task->activeAssigneeCount();

                if ((int) $this->input('max_assignees') < $current) {
                    $validator->errors()->add(
                        'max_assignees',
                        "This task already has {$current} active assignees; the limit cannot be lower.",
                    );
                }
            }
        });
    }

    /** @return array<string, mixed> */
    public function taskAttributes(): array
    {
        return $this->safe()->except(['checklist']);
    }
}
