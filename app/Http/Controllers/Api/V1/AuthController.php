<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Requests\Api\V1\UpdateProfileRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use App\Models\Voice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
     * Self-service volunteer sign-up from the mobile app.
     *
     * The account is created with NO role, so the new user is an ordinary
     * volunteer — the same standing an admin-provisioned volunteer has. Elevated
     * access is only ever granted later, in the admin panel, never claimed here.
     *
     * On success the response mirrors {@see login()}: a fresh token plus the
     * user, so the app lands the person straight into their signed-in profile
     * without a second round-trip to log in.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Store the images first. If a file were saved after the row and that
        // write failed, we would have a user pointing at a file that never
        // landed.
        $imagePath = $request->hasFile('profile_image')
            ? $request->file('profile_image')->store('profile-images', 'public')
            : null;

        $coverPath = $request->hasFile('cover_image')
            ? $request->file('cover_image')->store('cover-images', 'public')
            : null;

        $user = User::create([
            'name' => $data['name'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'country' => $data['country'],
            'address' => $data['address'],
            'password' => Hash::make($data['password']),
            'profile_image' => $imagePath,
            'cover_image' => $coverPath,
        ]);

        // Every self-registered account is a normal user. Assigned explicitly
        // rather than left roleless so the account type is a fact the admin
        // panel can filter and assign work by — and note that Volunteer is the
        // one role User::canAccessPanel() refuses, so this grants no admin
        // access whatsoever.
        $user->assignRole(User::VOLUNTEER_ROLE);

        $token = $user->createToken($data['device_name']);

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * Update the signed-in user's own profile.
     *
     * A blank password field means "leave it unchanged" — it is only re-hashed
     * when a new one is supplied. A newly uploaded avatar replaces the previous
     * file, and the old one is deleted so orphaned images do not accumulate on
     * disk.
     */
    public function updateProfile(UpdateProfileRequest $request): UserResource
    {
        $user = $request->user();
        $data = $request->validated();

        $user->fill([
            'name' => $data['name'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'country' => $data['country'],
            'address' => $data['address'],
        ]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if ($request->hasFile('profile_image')) {
            $newPath = $request->file('profile_image')->store('profile-images', 'public');

            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $user->profile_image = $newPath;
        }

        if ($request->hasFile('cover_image')) {
            $newCover = $request->file('cover_image')->store('cover-images', 'public');

            if ($user->cover_image) {
                Storage::disk('public')->delete($user->cover_image);
            }

            $user->cover_image = $newCover;
        }

        $user->save();

        return new UserResource($user);
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

    /**
     * Delete the caller's account.
     *
     * Apple's App Store guideline 5.1.1(v) requires any app that lets people
     * create an account to let them delete it from inside the app. We honour
     * that without throwing away the conservation record: a member's planted
     * trees and findings are the point of the platform, so those stay — but the
     * person is scrubbed off them.
     *
     * The account row is kept (deleting it would orphan every tree, finding,
     * comment and task that references it) and anonymised in place: every field
     * that identifies a human is wiped, the password is replaced with an
     * unguessable value, the login email is freed, roles and push tokens are
     * removed, and every access token is revoked. What remains cannot be logged
     * into and names nobody — the trees simply show "Former member".
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        DB::transaction(function () use ($user) {
            // Findings carry a denormalised author name/email of their own, so
            // clear those too — nulling the relation alone would leave the name
            // printed on the card.
            Voice::where('user_id', $user->id)->update([
                'author_name' => __('Former member'),
                'author_email' => null,
            ]);

            // No more notifications to a closed account, and no lingering role.
            $user->pushTokens()->delete();
            $user->syncRoles([]);

            // Erase the actual photo files, not just the columns that point at
            // them — an orphaned avatar on disk is still the person's face.
            foreach (array_filter([$user->profile_image, $user->cover_image]) as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            // Scrub the row itself. The e-mail is moved aside (kept unique) so
            // the original address is free to register again from scratch.
            $user->forceFill([
                'name' => __('Former'),
                'lastname' => __('member'),
                'email' => 'deleted_'.$user->id.'@removed.invalid',
                'country' => null,
                'address' => null,
                'profile_image' => null,
                'cover_image' => null,
                'password' => Hash::make(Str::random(40)),
            ])->save();

            // Sign the account out of every device and make its tokens useless.
            $user->tokens()->delete();
        });

        return response()->json([
            'message' => __('Your account has been deleted.'),
        ]);
    }
}
