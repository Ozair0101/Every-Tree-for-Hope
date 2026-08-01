<?php

namespace App\Http\Requests\Api\V1\Tasks;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Repositories\Tasks\TaskFilters;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates the task list query string.
 *
 * Filters are validated as strictly as a write body. An unvalidated `sort` is
 * an ORDER BY injection; an unvalidated `radius_km` is a bounding box that
 * selects the planet. Rejecting a bad filter with a 422 is also far kinder to
 * the client than silently ignoring it and returning a list that quietly means
 * something else.
 */
class IndexTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            // Accepts repeated keys (?status=a&status=b) and comma lists.
            'status' => ['sometimes'],
            'status.*' => [Rule::enum(TaskStatus::class)],
            'priority' => ['sometimes'],
            'priority.*' => [Rule::enum(TaskPriority::class)],

            'category_id' => ['sometimes', 'integer', 'exists:task_categories,id'],
            'event_id' => ['sometimes', 'integer', 'exists:events,id'],
            'assigned_to' => ['sometimes', 'integer', 'exists:users,id'],
            'created_by' => ['sometimes', 'integer', 'exists:users,id'],

            'search' => ['sometimes', 'string', 'max:120'],
            'overdue' => ['sometimes', 'boolean'],
            'unassigned' => ['sometimes', 'boolean'],

            'due_before' => ['sometimes', 'date'],
            'due_after' => ['sometimes', 'date'],

            // All three or none: a latitude without a radius cannot make a box.
            //
            // Deliberately `nullable` and not `sometimes`. `sometimes` skips
            // every rule on an absent field — including `required_with` — so a
            // request with only `latitude` would have passed validation and then
            // silently ignored the geo filter entirely, returning a list that
            // quietly meant something other than what was asked for.
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude,radius_km'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude,radius_km'],
            // Capped: an unbounded radius is a table scan wearing a filter's clothes.
            'radius_km' => ['nullable', 'numeric', 'min:0.1', 'max:500', 'required_with:latitude,longitude'],

            'sort' => ['sometimes', 'string', Rule::in([...TaskFilters::SORTABLE, 'urgency'])],
            'direction' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],

            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ];
    }

    /** Comma-separated lists are normalised before the `.*` rules run. */
    protected function prepareForValidation(): void
    {
        foreach (['status', 'priority'] as $key) {
            $value = $this->query($key);

            if (is_string($value) && str_contains($value, ',')) {
                $this->merge([$key => array_filter(array_map('trim', explode(',', $value)))]);
            } elseif (is_string($value) && $value !== '') {
                $this->merge([$key => [$value]]);
            }
        }
    }

    public function messages(): array
    {
        return [
            'radius_km.max' => 'A search radius may not exceed 500 km.',
            'sort.in' => 'That sort field is not available. Allowed: :values.',
        ];
    }

    public function filters(): TaskFilters
    {
        return TaskFilters::fromRequest($this);
    }
}
