<?php

namespace Tests\Feature\Api;

use App\Models\Tree;
use App\Models\TreeComment;
use App\Models\TreeImage;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Many photos per tree, and the community wall around them.
 *
 * The behaviours pinned here are the ones whose breakage is silent: a cover
 * that stops mirroring onto the parent row shows a stale photo on the map with
 * no error anywhere, and a reply threading bug only appears once a real
 * conversation exists.
 */
class TreeGalleryAndSocialTest extends TestCase
{
    use RefreshDatabase;

    private User $planter;

    private User $viewer;

    private User $moderator;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->planter = User::factory()->create();
        $this->viewer = User::factory()->create();
        $this->moderator = User::factory()->create();
        $this->moderator->assignRole('Manager');
    }

    private function tree(array $attributes = []): Tree
    {
        return $this->planter->trees()->create(array_merge([
            'species' => 'Chinar',
            'latitude' => 34.55,
            'longitude' => 69.05,
            'planted_on' => now()->subMonths(6)->toDateString(),
            'status' => 'approved',
            'location_name' => 'Qargha',
            'approved_at' => now(),
        ], $attributes));
    }

    private function image(Tree $tree, string $phase = TreeImage::PHASE_BEFORE, array $attributes = []): TreeImage
    {
        return $tree->images()->create(array_merge([
            'phase' => $phase,
            'path' => 'trees/'.uniqid().'.jpg',
        ], $attributes));
    }

    private function photo(string $name = 'planting.jpg'): UploadedFile
    {
        return UploadedFile::fake()->image($name, 900, 700);
    }

    /* ══════════════ AUTO-APPROVAL ══════════════ */

    public function test_a_volunteers_tree_still_waits_for_review(): void
    {
        $response = $this->actingAs($this->planter, 'sanctum')->postJson('/api/v1/trees', [
            'species' => 'Chinar',
            'latitude' => 34.55,
            'longitude' => 69.05,
            'planted_on' => now()->toDateString(),
        ]);

        $response->assertCreated();
        $this->assertSame('pending', $response->json('data.tree.status'));
    }

    public function test_a_tree_recorded_by_staff_is_published_immediately(): void
    {
        // Someone who may approve other people's trees would otherwise have to
        // approve their own as a second step with one possible outcome.
        $response = $this->actingAs($this->moderator, 'sanctum')->postJson('/api/v1/trees', [
            'species' => 'Chinar',
            'latitude' => 34.55,
            'longitude' => 69.05,
            'planted_on' => now()->toDateString(),
        ]);

        $response->assertCreated();
        $this->assertSame('approved', $response->json('data.tree.status'));
        $this->assertNotNull(Tree::latest('id')->first()->approved_at);
    }

    /* ══════════════ MULTIPLE PHOTOS ══════════════ */

    public function test_a_planting_can_carry_several_photos_with_a_chosen_cover(): void
    {
        $response = $this->actingAs($this->planter, 'sanctum')->post('/api/v1/trees', [
            'species' => 'Chinar',
            'latitude' => 34.55,
            'longitude' => 69.05,
            'planted_on' => now()->toDateString(),
            'images' => [$this->photo('a.jpg'), $this->photo('b.jpg'), $this->photo('c.jpg')],
            'cover_index' => 1,
        ]);

        $response->assertCreated();

        $tree = Tree::latest('id')->first();
        $this->assertSame(3, $tree->beforeImages()->count());

        // The cover is mirrored onto the parent row, which is what the map, the
        // admin panel and every existing list read.
        $cover = $tree->beforeImages()->where('is_cover', true)->sole();
        $this->assertSame($cover->path, $tree->image_path);
        $this->assertSame(1, $cover->sort_order - $tree->beforeImages()->min('sort_order'));
    }

    public function test_the_single_image_field_is_still_accepted(): void
    {
        // An app build that has not updated yet must keep working; its one photo
        // lands in the same gallery as everyone else's.
        $this->actingAs($this->planter, 'sanctum')->post('/api/v1/trees', [
            'species' => 'Chinar',
            'latitude' => 34.55,
            'longitude' => 69.05,
            'planted_on' => now()->toDateString(),
            'image' => $this->photo(),
        ])->assertCreated();

        $tree = Tree::latest('id')->first();
        $this->assertSame(1, $tree->beforeImages()->count());
        $this->assertNotNull($tree->image_path);
    }

    public function test_the_first_photo_becomes_the_cover_without_being_asked(): void
    {
        // The common case must need no decision from the planter.
        $this->actingAs($this->planter, 'sanctum')->post('/api/v1/trees', [
            'species' => 'Chinar',
            'latitude' => 34.55,
            'longitude' => 69.05,
            'planted_on' => now()->toDateString(),
            'images' => [$this->photo('a.jpg'), $this->photo('b.jpg')],
        ])->assertCreated();

        $tree = Tree::latest('id')->first();
        $this->assertSame(1, $tree->beforeImages()->where('is_cover', true)->count());
        $this->assertNotNull($tree->image_path);
    }

    public function test_a_phase_cannot_exceed_its_photo_limit(): void
    {
        $tree = $this->tree();

        for ($i = 0; $i < TreeImage::MAX_PER_PHASE; $i++) {
            $this->image($tree);
        }

        // Refused before anything is stored, so the planter is told the limit
        // rather than discovering half their upload was silently dropped.
        $this->actingAs($this->planter, 'sanctum')
            ->post("/api/v1/trees/{$tree->id}/images", ['images' => [$this->photo()]])
            ->assertStatus(422);

        $this->assertSame(TreeImage::MAX_PER_PHASE, $tree->beforeImages()->count());
    }

    /* ══════════════ AFTER PHOTOS ══════════════ */

    public function test_follow_up_photos_complete_the_comparison(): void
    {
        $tree = $this->tree();
        $this->image($tree);
        $tree->syncCover(TreeImage::PHASE_BEFORE);

        $this->actingAs($this->planter, 'sanctum')->post("/api/v1/trees/{$tree->id}/after-images", [
            'images' => [$this->photo('after-a.jpg'), $this->photo('after-b.jpg')],
            'cover_index' => 1,
        ])->assertCreated();

        $tree->refresh();

        $this->assertSame(2, $tree->afterImages()->count());
        $this->assertTrue($tree->hasComparison());
        $this->assertSame(
            $tree->afterImages()->where('is_cover', true)->sole()->path,
            $tree->after_image_path,
        );
    }

    public function test_follow_up_photos_are_refused_until_the_tree_is_approved(): void
    {
        // Until the planting has been accepted there is nothing for the
        // follow-up to be compared against.
        $tree = $this->tree(['status' => 'pending', 'approved_at' => null]);
        $this->image($tree);

        $this->actingAs($this->planter, 'sanctum')
            ->post("/api/v1/trees/{$tree->id}/after-images", ['images' => [$this->photo()]])
            ->assertStatus(422);
    }

    public function test_only_the_planter_may_add_photos(): void
    {
        $tree = $this->tree();
        $this->image($tree);

        // Moderators approve and reject trees; they do not curate someone
        // else's photographs.
        $this->actingAs($this->moderator, 'sanctum')
            ->post("/api/v1/trees/{$tree->id}/after-images", ['images' => [$this->photo()]])
            ->assertForbidden();
    }

    /* ══════════════ EDITING ══════════════ */

    public function test_the_planter_can_correct_their_own_record(): void
    {
        $tree = $this->tree(['species' => 'Chinar', 'notes' => 'By the gate']);

        $this->actingAs($this->planter, 'sanctum')
            ->putJson("/api/v1/trees/{$tree->id}", [
                'species' => 'Plane tree',
                'notes' => 'By the north gate',
                'location_name' => 'Paghman',
            ])
            ->assertOk();

        $tree->refresh();

        $this->assertSame('Plane tree', $tree->species);
        $this->assertSame('Paghman', $tree->location_name);
        // Editing a description is not a reason to hide a published record —
        // treating it as one would teach planters not to fix their mistakes.
        $this->assertSame('approved', $tree->status);
    }

    public function test_editing_cannot_move_a_tree(): void
    {
        // The coordinates were captured on site and are the evidence the record
        // rests on. Letting them be typed in afterwards turns a measurement into
        // a claim, so they are simply not in the accepted set.
        $tree = $this->tree();

        $this->actingAs($this->planter, 'sanctum')
            ->putJson("/api/v1/trees/{$tree->id}", [
                'species' => 'Chinar',
                'latitude' => 0.0,
                'longitude' => 0.0,
            ])
            ->assertOk();

        $tree->refresh();

        $this->assertEqualsWithDelta(34.55, (float) $tree->latitude, 0.0001);
        $this->assertEqualsWithDelta(69.05, (float) $tree->longitude, 0.0001);
    }

    public function test_nobody_else_can_edit_someone_elses_tree(): void
    {
        $tree = $this->tree();

        foreach ([$this->viewer, $this->moderator] as $intruder) {
            $this->actingAs($intruder, 'sanctum')
                ->putJson("/api/v1/trees/{$tree->id}", ['species' => 'Hijacked'])
                ->assertForbidden();
        }

        $this->assertSame('Chinar', $tree->fresh()->species);
    }

    /* ══════════════ COVER SELECTION ══════════════ */

    public function test_choosing_a_cover_demotes_only_its_own_phase(): void
    {
        $tree = $this->tree();
        $beforeCover = $this->image($tree, TreeImage::PHASE_BEFORE);
        $after = $this->image($tree, TreeImage::PHASE_AFTER);

        $tree->setCover($beforeCover);
        $tree->setCover($after);
        $tree->refresh();

        // Choosing an "after" cover must not clear the "before" one, or the
        // comparison loses half of itself.
        $this->assertSame($beforeCover->path, $tree->image_path);
        $this->assertSame($after->path, $tree->after_image_path);
    }

    public function test_deleting_the_cover_promotes_the_next_photo(): void
    {
        $tree = $this->tree();
        $first = $this->image($tree, TreeImage::PHASE_BEFORE, ['sort_order' => 1]);
        $second = $this->image($tree, TreeImage::PHASE_BEFORE, ['sort_order' => 2]);

        $tree->setCover($first);

        $this->actingAs($this->planter, 'sanctum')
            ->deleteJson("/api/v1/trees/{$tree->id}/images/{$first->id}")
            ->assertOk();

        $tree->refresh();

        $this->assertSame($second->path, $tree->image_path);
        $this->assertTrue($tree->beforeImages()->sole()->is_cover);
    }

    public function test_an_approved_tree_keeps_at_least_one_planting_photo(): void
    {
        // A public record with no photograph is a claim with no evidence.
        $tree = $this->tree();
        $only = $this->image($tree);
        $tree->syncCover(TreeImage::PHASE_BEFORE);

        $this->actingAs($this->planter, 'sanctum')
            ->deleteJson("/api/v1/trees/{$tree->id}/images/{$only->id}")
            ->assertStatus(422);
    }

    /* ══════════════ THE FEED ══════════════ */

    public function test_the_feed_shows_only_approved_trees(): void
    {
        $this->tree();
        $this->tree(['status' => 'pending', 'approved_at' => null]);
        $this->tree(['status' => 'rejected', 'approved_at' => null]);

        $response = $this->getJson('/api/v1/trees/feed')->assertOk();

        $this->assertCount(1, $response->json('data'));
    }

    public function test_the_feed_is_readable_without_signing_in(): void
    {
        // The work of a plantation programme is the argument for funding it;
        // hiding it behind a sign-in wall hides it from the people who matter.
        $this->tree();

        $response = $this->getJson('/api/v1/trees/feed')->assertOk();

        $this->assertFalse($response->json('data.0.is_liked'));
        $this->assertFalse($response->json('data.0.is_mine'));
    }

    public function test_the_feed_carries_both_covers_for_the_comparison(): void
    {
        $tree = $this->tree();
        $this->image($tree, TreeImage::PHASE_BEFORE);
        $this->image($tree, TreeImage::PHASE_AFTER, ['captured_at' => now()]);
        $tree->syncCover(TreeImage::PHASE_BEFORE);
        $tree->syncCover(TreeImage::PHASE_AFTER);

        $row = $this->getJson('/api/v1/trees/feed')->assertOk()->json('data.0');

        // Sent flat so a card can draw the side-by-side without loading either
        // gallery — twenty posts would otherwise be twenty gallery loads.
        $this->assertNotNull($row['image_url']);
        $this->assertNotNull($row['after_image_url']);
        $this->assertTrue($row['has_comparison']);
        $this->assertSame(1, $row['before_images_count']);
        $this->assertSame(1, $row['after_images_count']);
    }

    public function test_the_comparison_filter_hides_posts_with_no_follow_up(): void
    {
        $withBoth = $this->tree();
        $this->image($withBoth, TreeImage::PHASE_BEFORE);
        $this->image($withBoth, TreeImage::PHASE_AFTER);
        $withBoth->syncCover(TreeImage::PHASE_BEFORE);
        $withBoth->syncCover(TreeImage::PHASE_AFTER);

        $planted = $this->tree();
        $this->image($planted);
        $planted->syncCover(TreeImage::PHASE_BEFORE);

        $response = $this->getJson('/api/v1/trees/feed?with_comparison=1')->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertSame($withBoth->id, $response->json('data.0.id'));
    }

    /* ══════════════ LIKES ══════════════ */

    public function test_liking_toggles_and_reports_the_new_count(): void
    {
        $tree = $this->tree();

        $first = $this->actingAs($this->viewer, 'sanctum')
            ->postJson("/api/v1/trees/{$tree->id}/like")->assertOk();

        $this->assertTrue($first->json('data.is_liked'));
        $this->assertSame(1, $first->json('data.likes_count'));

        $second = $this->actingAs($this->viewer, 'sanctum')
            ->postJson("/api/v1/trees/{$tree->id}/like")->assertOk();

        $this->assertFalse($second->json('data.is_liked'));
        $this->assertSame(0, $second->json('data.likes_count'));
    }

    public function test_a_guest_cannot_like(): void
    {
        $tree = $this->tree();

        $this->postJson("/api/v1/trees/{$tree->id}/like")->assertUnauthorized();
    }

    public function test_the_feed_reports_whether_the_viewer_liked_each_post(): void
    {
        $tree = $this->tree();
        $this->actingAs($this->viewer, 'sanctum')->postJson("/api/v1/trees/{$tree->id}/like");

        // Resolved against the bearer token, so the heart is correct on a fresh
        // install that holds no local state.
        $mine = $this->actingAs($this->viewer, 'sanctum')->getJson('/api/v1/trees/feed');
        $theirs = $this->actingAs($this->planter, 'sanctum')->getJson('/api/v1/trees/feed');

        $this->assertTrue($mine->json('data.0.is_liked'));
        $this->assertFalse($theirs->json('data.0.is_liked'));
        $this->assertSame(1, $theirs->json('data.0.likes_count'));
    }

    public function test_sharing_increments_the_counter(): void
    {
        $tree = $this->tree();

        $response = $this->actingAs($this->viewer, 'sanctum')
            ->postJson("/api/v1/trees/{$tree->id}/share")->assertOk();

        $this->assertSame(1, $response->json('data.shares_count'));
    }

    /* ══════════════ COMMENTS ══════════════ */

    public function test_a_guest_cannot_comment_but_can_read(): void
    {
        $tree = $this->tree();
        TreeComment::post($tree, $this->viewer, 'Lovely.');

        $this->postJson("/api/v1/trees/{$tree->id}/comments", ['body' => 'Hi'])->assertUnauthorized();
        $this->getJson("/api/v1/trees/{$tree->id}/comments")->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_a_reply_to_a_reply_joins_the_same_thread(): void
    {
        $tree = $this->tree();

        $root = TreeComment::post($tree, $this->viewer, 'Beautiful work!');

        $reply = $this->actingAs($this->planter, 'sanctum')->postJson(
            "/api/v1/trees/{$tree->id}/comments",
            ['body' => 'Thank you!', 'parent_id' => $root->id],
        )->assertCreated()->json('data.comment');

        $nested = $this->actingAs($this->viewer, 'sanctum')->postJson(
            "/api/v1/trees/{$tree->id}/comments",
            ['body' => 'How tall is it now?', 'parent_id' => $reply['id']],
        )->assertCreated()->json('data.comment');

        // The thread stays two levels deep however far the conversation goes,
        // while the true conversational edge is preserved.
        $this->assertSame($root->id, $nested['root_id']);
        $this->assertSame($reply['id'], $nested['parent_id']);

        $thread = $this->getJson("/api/v1/trees/{$tree->id}/comments")->assertOk();

        $this->assertCount(1, $thread->json('data'));
        $this->assertCount(2, $thread->json('data.0.replies'));
        // The recipient is named, so a reply-to-a-reply does not read as
        // addressing the whole thread.
        $this->assertSame(
            trim($this->planter->name.' '.$this->planter->lastname),
            $thread->json('data.0.replies.1.reply_to'),
        );
    }

    public function test_a_reply_cannot_be_grafted_onto_another_trees_thread(): void
    {
        $mine = $this->tree();
        $other = $this->tree();
        $foreign = TreeComment::post($other, $this->viewer, 'Elsewhere');

        $this->actingAs($this->viewer, 'sanctum')->postJson(
            "/api/v1/trees/{$mine->id}/comments",
            ['body' => 'Sneaky', 'parent_id' => $foreign->id],
        )->assertStatus(422);
    }

    public function test_the_planter_may_remove_a_comment_from_their_own_post(): void
    {
        // It is their record; asking them to wait for staff to clear abuse from
        // it is the wrong answer.
        $tree = $this->tree();
        $comment = TreeComment::post($tree, $this->viewer, 'Rude thing');

        $this->actingAs($this->planter, 'sanctum')
            ->deleteJson("/api/v1/trees/comments/{$comment->id}")
            ->assertOk();

        $this->assertSoftDeleted('tree_comments', ['id' => $comment->id]);
    }

    public function test_a_stranger_cannot_remove_someone_elses_comment(): void
    {
        $tree = $this->tree();
        $comment = TreeComment::post($tree, $this->planter, 'Mine');
        $stranger = User::factory()->create();

        $this->actingAs($stranger, 'sanctum')
            ->deleteJson("/api/v1/trees/comments/{$comment->id}")
            ->assertForbidden();
    }

    public function test_removing_a_comment_leaves_its_replies_standing(): void
    {
        // Hard-deleting would cascade the replies away, which reads to everyone
        // else as the replies having been censored too.
        $tree = $this->tree();
        $root = TreeComment::post($tree, $this->viewer, 'Question?');
        $reply = TreeComment::post($tree, $this->planter, 'Answer.', $root);

        $this->actingAs($this->viewer, 'sanctum')
            ->deleteJson("/api/v1/trees/comments/{$root->id}")
            ->assertOk();

        $this->assertDatabaseHas('tree_comments', ['id' => $reply->id, 'deleted_at' => null]);
    }

    public function test_the_comment_count_on_the_feed_includes_replies(): void
    {
        $tree = $this->tree();
        $root = TreeComment::post($tree, $this->viewer, 'One');
        TreeComment::post($tree, $this->planter, 'Two', $root);

        $this->assertSame(2, $this->getJson('/api/v1/trees/feed')->json('data.0.comments_count'));
    }
}
