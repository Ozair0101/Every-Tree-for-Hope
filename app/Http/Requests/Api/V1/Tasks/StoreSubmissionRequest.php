<?php

namespace App\Http\Requests\Api\V1\Tasks;

use App\Enums\TaskAttachmentType;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Turning work in from the field.
 *
 * multipart/form-data — the proof files come with it. The MIME allow-list and
 * size ceilings come from {@see TaskAttachmentType} rather than being retyped
 * here, so the upload endpoint and the model can never disagree about what is
 * acceptable.
 */
class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('submit', $this->route('assignment')) ?? false;
    }

    public function rules(): array
    {
        return [
            'note' => ['nullable', 'string', 'max:5000'],
            'hours_spent' => ['nullable', 'numeric', 'min:0', 'max:999.99'],

            // Proof of presence. Optional at this layer: whether a missing
            // reading is acceptable depends on the task's `requires_geo_check`,
            // which the service knows and this request does not.
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'gps_accuracy' => ['nullable', 'integer', 'min:0', 'max:100000'],
            // Reverse-geocoded on the device. Stored as sent rather than looked
            // up server-side: the volunteer's handset already resolved it, and
            // repeating the call would be a network round trip per submission.
            'address' => ['nullable', 'string', 'max:255'],
            // Android reports whether the fix came from a mock provider. Trusted
            // as a signal, never as a verdict — a rooted device can lie about
            // this too, which is why it is one input among several.
            'is_mocked' => ['nullable', 'boolean'],
            'device_captured_at' => ['nullable', 'date'],

            // The offline idempotency key. Optional, because the admin panel has
            // no client to generate one.
            'client_uuid' => ['nullable', 'uuid'],

            'files' => ['sometimes', 'array', 'max:10'],
            'files.*' => [
                'file',
                'mimetypes:'.implode(',', TaskAttachmentType::allAllowedMimeTypes()),
                // The largest ceiling any category allows; the per-type limit is
                // enforced when the file is classified and stored.
                'max:'.TaskAttachmentType::VIDEO->maxSizeKb(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'files.*.mimetypes' => 'Only photos, videos, documents and audio may be attached.',
            'files.max' => 'At most 10 files may be sent with one submission.',
            'client_uuid.uuid' => 'The idempotency key must be a UUID.',
        ];
    }

    /** @return array<string, mixed> */
    public function payload(): array
    {
        return $this->safe()->only([
            'note', 'hours_spent', 'latitude', 'longitude', 'gps_accuracy',
            'address', 'is_mocked', 'device_captured_at',
        ]);
    }

    /**
     * The uploaded proof files.
     *
     * Not named `files()` — that is an inherited method on the base request
     * with a different signature, and overriding it is a fatal error rather
     * than a subtle bug.
     *
     * @return array<int, \Illuminate\Http\UploadedFile>
     */
    public function uploadedFiles(): array
    {
        return $this->file('files', []);
    }
}
