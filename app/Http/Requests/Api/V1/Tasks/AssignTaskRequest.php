<?php

namespace App\Http\Requests\Api\V1\Tasks;

use App\Enums\TaskAssignmentRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('assign', $this->route('task')) ?? false;
    }

    public function rules(): array
    {
        return [
            'user_ids' => ['required', 'array', 'min:1', 'max:100'],
            'user_ids.*' => ['integer', 'distinct', 'exists:users,id'],
            'role' => ['sometimes', Rule::enum(TaskAssignmentRole::class)],
            // Must be one of the people being assigned — naming a primary who is
            // not on the task would leave the flag pointing at nobody.
            'primary_user_id' => ['sometimes', 'integer', 'in_array:user_ids.*'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_ids.*.distinct' => 'The same person was listed twice.',
            'primary_user_id.in_array' => 'The primary assignee must be one of the people being assigned.',
        ];
    }

    public function role(): TaskAssignmentRole
    {
        return TaskAssignmentRole::from($this->input('role', TaskAssignmentRole::ASSIGNEE->value));
    }
}
