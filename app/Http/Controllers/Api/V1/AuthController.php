<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Token-based authentication for the mobile client.
 *
 * The web platform authenticates with cookie-backed sessions via Filament; the
 * mobile app cannot use those, so it exchanges credentials for a Sanctum
 * personal access token and presents it as a bearer token thereafter.
 *
 * Note this is intentionally decoupled from {@see User::canAccessPanel()}.
 * Panel access requires an assigned admin role, but volunteers are ordinary
 * users with no such role — they must still be able to sign in on mobile.
 */
class AuthController extends Controller
{
    /**
     * Exchange email/password for a personal access token.
     *
     * A previously issued token for the same device name is revoked first, so a
     * device that reinstalls the app does not accumulate orphaned tokens.
     *
     * @throws ValidationException when the credentials do not match
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->string('email'))->first();

        // A single generic failure for both "no such user" and "wrong password"
        // — distinguishing them would let an attacker enumerate valid accounts.
        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('The provided credentials are incorrect.')],
            ]);
        }

        $deviceName = $request->string('device_name')->toString();

        $user->tokens()->where('name', $deviceName)->delete();

        $token = $user->createToken($deviceName);

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Revoke only the token used to make this request, leaving the user's other
     * devices signed in.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => __('Signed out successfully.'),
        ]);
    }

    /**
     * The current user — used by the app on cold start to confirm a stored token
     * is still valid and to refresh cached roles and permissions.
     */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
