<?php

namespace App\Http\Requests\Api\V1\Tasks;

use App\Enums\TaskAttachmentType;
use Illuminate\Foundation\Http\FormRequest;

class StoreProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('reportProgress', $this->route('assignment')) ?? false;
    }

    public function rules(): array
    {
        return [
            // Validated to 0–100 as well as clamped in the model. The clamp
            // stops a rounding artefact corrupting data; this tells a client
            // sending 400 that it has a bug, rather than silently accepting it.
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'note' => ['nullable', 'string', 'max:2000'],

            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],

            'client_uuid' => ['nullable', 'uuid'],

            'files' => ['sometimes', 'array', 'max:5'],
            'files.*' => [
                'file',
                'mimetypes:'.implode(',', TaskAttachmentType::IMAGE->allowedMimeTypes()),
                'max:'.TaskAttachmentType::IMAGE->maxSizeKb(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'files.*.mimetypes' => 'Progress updates accept photos only.',
            'progress_percentage.max' => 'Progress cannot exceed 100%.',
        ];
    }

    /** @return array<int, \Illuminate\Http\UploadedFile> */
    public function uploadedFiles(): array
    {
        return $this->file('files', []);
    }
}
