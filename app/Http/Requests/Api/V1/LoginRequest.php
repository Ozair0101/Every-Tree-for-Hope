<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Credentials supplied by a mobile client in exchange for a Sanctum token.
 *
 * `device_name` is required so that every issued token is attributable to a
 * physical device. That makes selective revocation possible ("sign out my old
 * phone") without invalidating every session the user holds.
 */
class LoginRequest extends FormRequest
{
    /**
     * Anyone may attempt to log in; the credentials themselves are the gate.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'device_name.required' => 'A device name is required so the session can be identified later.',
        ];
    }
}
