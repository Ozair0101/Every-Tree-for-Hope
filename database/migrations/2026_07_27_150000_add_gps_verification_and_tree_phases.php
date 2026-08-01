<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

/**
 * GPS verification, the volunteer role, and before/after tree photography.
 *
 * ── GPS ──────────────────────────────────────────────────────────────────────
 * Proof of presence already stored a reading and a distance. What it could not
 * do is say whether the reading was *believable*. Three columns change that:
 *
 *   address       what the coordinates resolve to, captured on the device at the
 *                 moment of submission. Stored rather than looked up on demand
 *                 because a reviewer reading the record in six months needs the
 *                 place as it was named then, and because reverse geocoding
 *                 every row on a report screen is a per-row network call.
 *
 *   is_mocked     Android reports whether a fix came from a mock provider.
 *                 That flag is the single strongest signal a location was
 *                 faked, and it costs nothing to carry.
 *
 *   verification  the server's own findings — impossible travel speed,
 *                 suspiciously perfect accuracy, a device clock that disagrees
 *                 with the server. Kept as JSON because these are evidence for
 *                 a human, not something queried on.
 *
 * ── The volunteer role ───────────────────────────────────────────────────────
 * Registered users had no role at all. That worked, but "no role" is not a
 * queryable fact: an admin picking people to assign a task to could not filter
 * for them, and nothing distinguished a volunteer from an account whose role was
 * removed by mistake. `Volunteer` makes the account type explicit.
 *
 * Note the paired change in User::canAccessPanel(). Until now *any* role opened
 * the admin panel, so introducing this role without that fix would have handed
 * every newly registered member of the public a Filament login.
 *
 * ── Before and after ─────────────────────────────────────────────────────────
 * A tree's whole point is the change over time. The planting photo and the
 * photo taken a season later belong to the same record — two rows would make
 * "show me the difference" a join and a guess about which pair to compare.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Task submissions: the verified artefact ──────────────────────────
        Schema::table('task_submissions', function (Blueprint $table) {
            $table->string('address')->nullable()->after('gps_accuracy');
            $table->boolean('is_mocked')->nullable()->after('is_within_geofence');
            $table->json('verification')->nullable()->after('is_mocked');

            // The reviewer's "show me anything doubtful" query.
            $table->index(['is_mocked', 'created_at']);
        });

        // Progress reports carry a reading too, and a trail of addresses is
        // what makes a long task auditable after the fact.
        Schema::table('task_progress', function (Blueprint $table) {
            $table->string('address')->nullable()->after('gps_accuracy');
            $table->boolean('is_mocked')->nullable()->after('address');
        });

        // ── Trees: same verification, plus the after photo ───────────────────
        Schema::table('trees', function (Blueprint $table) {
            $table->string('address')->nullable()->after('gps_accuracy');
            $table->boolean('is_mocked')->nullable()->after('address');

            // `image_path` stays the planting photo — renaming a live column
            // would break the mobile app, the admin panel and the public map at
            // once for no gain. The comment on the model says which is which.
            $table->string('after_image_path')->nullable()->after('image_path');
            $table->text('after_image_note')->nullable()->after('after_image_path');
            $table->timestamp('after_image_taken_at')->nullable()->after('after_image_note');

            // "Which trees have a follow-up?" — the growth gallery's query.
            $table->index(['user_id', 'after_image_taken_at']);
        });

        $this->createVolunteerRole();
    }

    /**
     * Create the role and backfill everyone who should hold it.
     *
     * Guarded on the roles table existing so a `migrate:fresh` that has not yet
     * reached the permission migration cannot fail here.
     */
    private function createVolunteerRole(): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        $volunteer = Role::findOrCreate('Volunteer', 'web');

        // Every existing roleless account is, by definition, a registered
        // volunteer — that is exactly what "no role" has meant until now.
        User::query()
            ->whereDoesntHave('roles')
            ->each(fn (User $user) => $user->assignRole($volunteer));
    }

    public function down(): void
    {
        Schema::table('trees', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'after_image_taken_at']);
            $table->dropColumn([
                'address', 'is_mocked',
                'after_image_path', 'after_image_note', 'after_image_taken_at',
            ]);
        });

        Schema::table('task_progress', function (Blueprint $table) {
            $table->dropColumn(['address', 'is_mocked']);
        });

        Schema::table('task_submissions', function (Blueprint $table) {
            $table->dropIndex(['is_mocked', 'created_at']);
            $table->dropColumn(['address', 'is_mocked', 'verification']);
        });

        // The role is deliberately left in place: dropping it would strip the
        // account type from every registered user, and re-running up() cannot
        // tell which of them was roleless on purpose.
    }
};
