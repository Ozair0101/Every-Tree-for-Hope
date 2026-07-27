<?php

namespace Tests\Feature\Media;

use App\Filament\Pages\TreeComparison;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\TaskSubmission;
use App\Models\Tree;
use App\Models\User;
use App\Services\Media\ImageProcessingService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Phase 9 — photo verification.
 *
 * Exercises real image bytes through GD rather than stubbing the processor: the
 * things that break here are EXIF parsing and encoder edge cases, and a mocked
 * service would prove none of it.
 */
class PhotoVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $volunteer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Manager');

        $this->volunteer = User::factory()->create(['name' => 'Ahmad']);
        $this->volunteer->assignRole(User::VOLUNTEER_ROLE);

        Storage::fake('public');
    }

    private function submission(): TaskSubmission
    {
        $task = Task::create(['title' => 'Plant saplings', 'created_by' => $this->admin->id]);
        $assignment = $task->assign($this->volunteer, $this->admin);

        return TaskSubmission::create([
            'task_id' => $task->id,
            'task_assignment_id' => $assignment->id,
            'user_id' => $this->volunteer->id,
            'attempt' => 1,
        ]);
    }

    /** A JPEG large enough to trip the compression threshold. */
    private function largePhoto(string $name = 'proof.jpg'): UploadedFile
    {
        return UploadedFile::fake()->image($name, 2400, 1800);
    }

    /* ══════════════ THREE COPIES ══════════════ */

    public function test_an_uploaded_photo_is_stored_as_original_compressed_and_thumbnail(): void
    {
        $submission = $this->submission();

        $attachment = TaskAttachment::storeFor($submission, $this->largePhoto(), $this->volunteer);

        $this->assertNotNull($attachment->original_path);
        $this->assertNotNull($attachment->thumbnail_path);
        $this->assertNotNull($attachment->file_path);

        Storage::disk('public')->assertExists($attachment->original_path);
        Storage::disk('public')->assertExists($attachment->thumbnail_path);
        Storage::disk('public')->assertExists($attachment->file_path);

        // The evidential copy must never be the same bytes as the re-encoded one.
        $this->assertNotSame($attachment->original_path, $attachment->file_path);
    }

    public function test_the_compressed_copy_is_smaller_than_the_original(): void
    {
        $submission = $this->submission();

        $attachment = TaskAttachment::storeFor($submission, $this->largePhoto(), $this->volunteer);

        $this->assertNotNull($attachment->original_size);
        $this->assertLessThan($attachment->original_size, $attachment->file_size);
        $this->assertGreaterThan(0, $attachment->compressionRatio());
    }

    public function test_the_thumbnail_is_a_small_square(): void
    {
        $submission = $this->submission();

        $attachment = TaskAttachment::storeFor($submission, $this->largePhoto(), $this->volunteer);

        $bytes = Storage::disk('public')->get($attachment->thumbnail_path);
        [$width, $height] = getimagesizefromstring($bytes);

        // Square, so a grid never shows a stretched sapling.
        $this->assertSame($width, $height);
        $this->assertLessThanOrEqual(320, $width);
    }

    public function test_a_small_image_is_not_re_encoded(): void
    {
        $submission = $this->submission();

        // Below the compression threshold: re-encoding would strip EXIF and add
        // loss for no saving, so the original is served directly.
        $small = UploadedFile::fake()->image('small.jpg', 200, 200);

        $attachment = TaskAttachment::storeFor($submission, $small, $this->volunteer);

        $this->assertSame($attachment->original_path, $attachment->file_path);
        $this->assertNotNull($attachment->thumbnail_path);
    }

    public function test_deleting_an_attachment_removes_all_three_files(): void
    {
        $submission = $this->submission();

        $attachment = TaskAttachment::storeFor($submission, $this->largePhoto(), $this->volunteer);

        $paths = [$attachment->file_path, $attachment->original_path, $attachment->thumbnail_path];

        $attachment->delete();

        // The original is the largest of the three — leaking it would be the
        // expensive mistake.
        foreach ($paths as $path) {
            Storage::disk('public')->assertMissing($path);
        }
    }

    public function test_a_non_image_is_stored_untouched(): void
    {
        $submission = $this->submission();

        $pdf = UploadedFile::fake()->create('plan.pdf', 400, 'application/pdf');

        $attachment = TaskAttachment::storeFor($submission, $pdf, $this->volunteer);

        // Nothing to resize and no EXIF to read; pushing it through GD would be
        // pure cost.
        $this->assertNull($attachment->thumbnail_path);
        $this->assertNull($attachment->original_path);
        Storage::disk('public')->assertExists($attachment->file_path);
    }

    /* ══════════════ METADATA ══════════════ */

    public function test_client_metadata_is_recorded_when_the_photo_carries_no_exif(): void
    {
        $submission = $this->submission();

        $attachment = TaskAttachment::storeFor(
            $submission,
            $this->largePhoto(),
            $this->volunteer,
            null,
            'public',
            [
                'latitude' => 34.5553,
                'longitude' => 69.0430,
                'gps_accuracy' => 8,
                'captured_at' => now()->subMinutes(5)->toIso8601String(),
                'device_make' => 'Samsung',
                'device_model' => 'SM-A155F',
                'device_os' => 'Android 14',
                'app_version' => '1.4.2',
            ],
        );

        $this->assertSame(34.5553, (float) $attachment->latitude);
        $this->assertSame(69.0430, (float) $attachment->longitude);
        $this->assertSame(8, $attachment->gps_accuracy);
        $this->assertNotNull($attachment->captured_at);
        $this->assertSame('Samsung', $attachment->device_make);
        $this->assertSame('SM-A155F', $attachment->device_model);
        $this->assertSame('1.4.2', $attachment->app_version);

        // A fake image carries no EXIF, so the source is honest about where
        // these values came from.
        $this->assertSame('client', $attachment->metadata_source);
        $this->assertFalse($attachment->metadataIsFromCamera());
    }

    public function test_a_photo_with_no_metadata_at_all_records_none(): void
    {
        $submission = $this->submission();

        $attachment = TaskAttachment::storeFor($submission, $this->largePhoto(), $this->volunteer);

        $this->assertSame('none', $attachment->metadata_source);
        $this->assertNull($attachment->latitude);
        $this->assertNull($attachment->device_make);
    }

    public function test_the_device_label_reads_as_one_line(): void
    {
        $submission = $this->submission();

        $attachment = TaskAttachment::storeFor(
            $submission, $this->largePhoto(), $this->volunteer, null, 'public',
            ['device_make' => 'Samsung', 'device_model' => 'SM-A155F', 'device_os' => 'Android 14'],
        );

        $this->assertSame('Samsung SM-A155F · Android 14', $attachment->deviceLabel());
    }

    /* ══════════════ EXIF PARSING ══════════════ */

    public function test_exif_gps_is_converted_from_degrees_minutes_seconds(): void
    {
        $service = app(ImageProcessingService::class);

        $method = new \ReflectionMethod($service, 'gpsFromExif');
        $method->setAccessible(true);

        // 34° 33' 19.08" N, 69° 2' 34.8" E — Qargha, expressed the way a camera
        // writes it: rational strings, hemisphere in a separate tag.
        $result = $method->invoke($service, [
            'GPSLatitude' => ['34/1', '33/1', '1908/100'],
            'GPSLatitudeRef' => 'N',
            'GPSLongitude' => ['69/1', '2/1', '348/10'],
            'GPSLongitudeRef' => 'E',
        ]);

        $this->assertEqualsWithDelta(34.5553, $result['latitude'], 0.001);
        $this->assertEqualsWithDelta(69.0430, $result['longitude'], 0.001);
    }

    public function test_southern_and_western_hemispheres_are_negative(): void
    {
        $service = app(ImageProcessingService::class);
        $method = new \ReflectionMethod($service, 'gpsFromExif');
        $method->setAccessible(true);

        $result = $method->invoke($service, [
            'GPSLatitude' => ['33/1', '55/1', '0/1'],
            'GPSLatitudeRef' => 'S',
            'GPSLongitude' => ['18/1', '25/1', '0/1'],
            'GPSLongitudeRef' => 'W',
        ]);

        // Missing this would put Cape Town in the Mediterranean.
        $this->assertLessThan(0, $result['latitude']);
        $this->assertLessThan(0, $result['longitude']);
    }

    public function test_a_null_island_reading_is_discarded(): void
    {
        $service = app(ImageProcessingService::class);
        $method = new \ReflectionMethod($service, 'gpsFromExif');
        $method->setAccessible(true);

        // A camera with no fix writes 0,0. That is the Atlantic, not a reading —
        // treating it as one puts pins in the ocean.
        $result = $method->invoke($service, [
            'GPSLatitude' => ['0/1', '0/1', '0/1'],
            'GPSLatitudeRef' => 'N',
            'GPSLongitude' => ['0/1', '0/1', '0/1'],
            'GPSLongitudeRef' => 'E',
        ]);

        $this->assertNull($result['latitude']);
        $this->assertNull($result['longitude']);
    }

    public function test_an_absurd_camera_clock_is_rejected(): void
    {
        $service = app(ImageProcessingService::class);
        $method = new \ReflectionMethod($service, 'parseDate');
        $method->setAccessible(true);

        // A dead backup battery reports 1970 or 2000.
        $this->assertNull($method->invoke($service, '1970-01-01 00:00:00'));
        // And a clock set forward is not a capture time either.
        $this->assertNull($method->invoke($service, now()->addYear()->toDateTimeString()));
        // A believable one survives.
        $this->assertNotNull($method->invoke($service, now()->subDay()->toDateTimeString()));
    }

    /* ══════════════ SUBMISSION FLOW ══════════════ */

    public function test_photos_uploaded_with_a_submission_carry_the_readings(): void
    {
        $submission = $this->submission();

        TaskAttachment::storeFor(
            $submission, $this->largePhoto('a.jpg'), $this->volunteer, null, 'public',
            ['latitude' => 34.5553, 'longitude' => 69.0430, 'device_make' => 'Samsung'],
        );

        $attachment = $submission->fresh()->attachments->first();

        $this->assertSame(34.5553, (float) $attachment->latitude);
        $this->assertSame('Samsung', $attachment->device_make);
        $this->assertNotNull($attachment->thumbnail_url);
        $this->assertNotNull($attachment->original_url);
    }

    /* ══════════════ TREE BEFORE / AFTER ══════════════ */

    private function tree(array $overrides = []): Tree
    {
        return Tree::create(array_merge([
            'user_id' => $this->volunteer->id,
            'species' => 'Chinar',
            'latitude' => 34.5553,
            'longitude' => 69.0430,
            'planted_on' => now()->subMonths(8)->toDateString(),
            'image_path' => 'trees/before.jpg',
            'status' => 'approved',
        ], $overrides));
    }

    public function test_the_after_photo_is_processed_and_located(): void
    {
        $tree = $this->tree();

        $this->actingAs($this->volunteer);

        $this->post("/api/v1/trees/{$tree->id}/after-image", [
            'image' => $this->largePhoto('after.jpg'),
            'note' => 'Eight months on.',
            'latitude' => 34.5554,
            'longitude' => 69.0431,
            'device_make' => 'Samsung',
            'device_model' => 'SM-A155F',
        ], ['Accept' => 'application/json'])->assertCreated();

        $tree->refresh();

        $this->assertNotNull($tree->after_image_path);
        $this->assertNotNull($tree->after_image_thumbnail_path);
        $this->assertSame(34.5554, (float) $tree->after_image_latitude);
        // Roughly 15m from the tree — close enough that nothing is flagged.
        $this->assertLessThan(50, $tree->after_image_distance);
        $this->assertStringContainsString('Samsung', $tree->after_image_device);
    }

    public function test_a_follow_up_shot_far_from_the_tree_records_the_distance(): void
    {
        $tree = $this->tree();

        $this->actingAs($this->volunteer);

        $this->post("/api/v1/trees/{$tree->id}/after-image", [
            'image' => $this->largePhoto('after.jpg'),
            'latitude' => 34.5800,
            'longitude' => 69.0900,
        ], ['Accept' => 'application/json'])->assertCreated();

        // Recorded, not refused — the reviewer decides what a 5km gap means.
        $this->assertGreaterThan(1000, $tree->fresh()->after_image_distance);
    }

    public function test_replacing_the_after_photo_removes_its_thumbnail_too(): void
    {
        $tree = $this->tree();

        $this->actingAs($this->volunteer);

        $this->post("/api/v1/trees/{$tree->id}/after-image",
            ['image' => $this->largePhoto('first.jpg')], ['Accept' => 'application/json']);

        $tree->refresh();
        $oldImage = $tree->after_image_path;
        $oldThumb = $tree->after_image_thumbnail_path;

        $this->post("/api/v1/trees/{$tree->id}/after-image",
            ['image' => $this->largePhoto('second.jpg')], ['Accept' => 'application/json']);

        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertMissing($oldThumb);
    }

    /* ══════════════ ADMIN COMPARISON ══════════════ */

    public function test_the_comparison_page_shows_paired_trees(): void
    {
        $paired = $this->tree(['species' => 'Chinar with follow-up']);
        $paired->update([
            'after_image_path' => 'trees/after.jpg',
            'after_image_taken_at' => now(),
        ]);

        $this->tree(['species' => 'Pine awaiting follow-up']);

        Livewire::actingAs($this->admin)
            ->test(TreeComparison::class)
            ->assertOk()
            ->assertSee('Chinar with follow-up')
            ->assertDontSee('Pine awaiting follow-up');
    }

    public function test_the_comparison_page_can_list_trees_still_awaiting_a_follow_up(): void
    {
        $paired = $this->tree(['species' => 'Chinar done']);
        $paired->update(['after_image_path' => 'trees/after.jpg', 'after_image_taken_at' => now()]);

        $this->tree(['species' => 'Pine pending']);

        Livewire::actingAs($this->admin)
            ->test(TreeComparison::class)
            ->set('filter', 'awaiting')
            ->assertSee('Pine pending')
            ->assertDontSee('Chinar done');
    }

    public function test_the_comparison_page_shows_the_growth_period(): void
    {
        $tree = $this->tree();
        $tree->update([
            'after_image_path' => 'trees/after.jpg',
            'after_image_taken_at' => now(),
        ]);

        Livewire::actingAs($this->admin)
            ->test(TreeComparison::class)
            ->assertSee('months of growth');
    }

    public function test_a_displaced_follow_up_is_flagged_on_the_comparison(): void
    {
        $tree = $this->tree();
        $tree->update([
            'after_image_path' => 'trees/after.jpg',
            'after_image_taken_at' => now(),
            'after_image_distance' => 4200,
        ]);

        $page = Livewire::actingAs($this->admin)->test(TreeComparison::class);

        $page->assertSee('4.2 km from the tree');
        $this->assertTrue($page->instance()->looksDisplaced($tree->fresh()));
    }

    public function test_a_nearby_follow_up_is_not_flagged(): void
    {
        $tree = $this->tree();
        $tree->update(['after_image_distance' => 12]);

        $page = Livewire::actingAs($this->admin)->test(TreeComparison::class);

        // A photographer steps back to frame a grown tree, and GPS drifts under
        // a canopy — twelve metres is not suspicious.
        $this->assertFalse($page->instance()->looksDisplaced($tree->fresh()));
    }

    public function test_the_comparison_page_is_closed_to_a_volunteer(): void
    {
        $this->actingAs($this->volunteer);

        $this->assertFalse(TreeComparison::canAccess());
    }

    public function test_the_comparison_page_searches_by_species_and_planter(): void
    {
        $chinar = $this->tree(['species' => 'Chinar']);
        $chinar->update(['after_image_path' => 'a.jpg', 'after_image_taken_at' => now()]);

        $pine = $this->tree(['species' => 'Deodar Pine']);
        $pine->update(['after_image_path' => 'b.jpg', 'after_image_taken_at' => now()]);

        Livewire::actingAs($this->admin)
            ->test(TreeComparison::class)
            ->set('search', 'Deodar')
            ->assertSee('Deodar Pine')
            ->assertDontSee('Chinar');
    }
}
