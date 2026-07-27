<?php

namespace App\Http\Requests\Api\V1\Tasks;

use App\Enums\TaskReviewStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Recording a verdict on submitted work.
 *
 * Authorises against the *task*, not the assignment: `TaskPolicy::review()`
 * knows that a named reviewer may review without holding the global permission,
 * and that nobody reviews their own submission whatever they hold.
 */
class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $assignment = $this->route('assignment');

        return $this->user()?->can('review', $assignment?->task) ?? false;
    }

    public function rules(): array
    {
        return [
            'review_status' => ['required', Rule::enum(TaskReviewStatus::class)],
            'score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comments' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $status = $this->input('review_status');

            // Sending work back without saying why is the single most common
            // complaint volunteers have about systems like this. The volunteer
            // cannot fix what they were not told.
            if (in_array($status, [TaskReviewStatus::REJECTED->value, TaskReviewStatus::NEEDS_REVISION->value], true)
                && blank($this->input('comments'))) {
                $validator->errors()->add(
                    'comments',
                    'Explain what needs fixing — a rejection without a reason cannot be acted on.',
                );
            }
        });
    }

    public function reviewStatus(): TaskReviewStatus
    {
        return TaskReviewStatus::from($this->input('review_status'));
    }
}
