<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * A new volunteer account created from the mobile app.
 *
 * This is the self-service counterpart to admin-provisioned accounts: it grants
 * no role, so the user signs in as an ordinary volunteer (see AuthController).
 *
 * `device_name` is required for the same reason as on login — the successful
 * response issues a Sanctum token immediately so the user lands signed in, and
 * every token must be attributable to a device for later selective revocation.
 *
 * The avatar arrives as a multipart file, so the whole request is
 * multipart/form-data, not JSON.
 */
class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * TEMPORARY DIAGNOSTIC — remove once the avatar upload is confirmed working.
     *
     * Runs before validation, so it captures what the server actually received
     * regardless of whether the `image` rule then rejects it. This tells us
     * definitively whether `profile_image` arrived as a real uploaded file, a
     * string, or nothing at all.
     */
    protected function prepareForValidation(): void
    {
        $file = $this->file('profile_image');
        $raw = $this->input('profile_image');

        Log::info('[register-debug] incoming request', [
            'content_type' => $this->header('Content-Type'),
            'has_file' => $this->hasFile('profile_image'),
            'input_keys' => array_keys($this->all()),
            'file_keys' => array_keys($this->allFiles()),
            'profile_image_is_file' => $file !== null,
            'profile_image_size' => $file?->getSize(),
            'profile_image_client_mime' => $file?->getClientMimeType(),
            'profile_image_real_mime' => $file && $file->isValid() ? $file->getMimeType() : null,
            'profile_image_as_input' => is_string($raw) ? substr($raw, 0, 150) : gettype($raw),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'country' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            // `confirmed` pairs with password_confirmation — a mistyped password
            // is unrecoverable here (there is no "back to the login you know"),
            // so it is worth the extra field at sign-up.
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // 5 MB ceiling: the client already downscales and compresses avatars,
            // so anything larger is almost certainly an un-processed original.
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'device_name' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'An account with this email already exists.',
            'password.confirmed' => 'The two passwords do not match.',
            'device_name.required' => 'A device name is required so the session can be identified later.',
        ];
    }
}
