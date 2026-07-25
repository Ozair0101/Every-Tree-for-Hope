<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Edits to the signed-in user's own profile.
 *
 * The rules mirror {@see RegisterRequest} with two differences: the password is
 * optional (an unchanged password must not be re-entered on every edit), and the
 * email uniqueness check ignores the current user's own row so saving without
 * changing the email does not collide with itself.
 *
 * Laravel does not parse a multipart body on PUT/PATCH, so this is submitted as
 * POST with method spoofing — see the route definition.
 */
class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Route is behind auth:sanctum; a user may always edit their own record.
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
            'country' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
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
        ];
    }
}
