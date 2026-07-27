<?php

namespace App\Models;

use App\Enums\TaskAttachmentType;
use App\Services\Media\ImageProcessingService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * A photo, video, document or audio file attached to task work.
 *
 * One polymorphic table rather than one per owner: the upload endpoint, the MIME
 * and size validation, the URL accessor and the orphan-cleanup job are identical
 * whether the file hangs off a task brief, a submission or a comment.
 *
 *   Task            reference material from the office — site map, species sheet
 *   TaskSubmission  the volunteer's proof photos for one attempt
 *   TaskComment     a picture attached to a question from the field
 *
 * `task_assignment_id` is carried alongside the morph as a denormalised index:
 * the morph says precisely *what* the file belongs to (which matters, because
 * attempt 1 was rejected for dark photos and attempt 2 has new ones), while the
 * assignment column answers "every file this volunteer uploaded for this job" in
 * one indexed query.
 *
 * Stores a relative path plus the disk name — never a full URL — so the storage
 * host can change without a data migration. Same rule as `users.profile_image`
 * and `trees.image_path`.
 */
class TaskAttachment extends Model
{
    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'task_id',
        'task_assignment_id',
        'uploaded_by',
        'disk',
        'file_path',
        'file_name',
        'file_type',
        'mime_type',
        'file_size',
        'width',
        'height',
        'duration_seconds',
        'latitude',
        'longitude',
        'gps_accuracy',
        'captured_at',
        'device_make',
        'device_model',
        'device_os',
        'app_version',
        'metadata_source',
        'exif',
        'original_path',
        'thumbnail_path',
        'original_size',
    ];

    protected $casts = [
        'file_type' => TaskAttachmentType::class,
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'duration_seconds' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'gps_accuracy' => 'integer',
        'captured_at' => 'datetime',
        'exif' => 'array',
        'original_size' => 'integer',
    ];

    protected $attributes = [
        'disk' => 'public',
    ];

    /**
     * Delete the physical file when the row goes, so storage cannot leak.
     *
     * Deliberately on `deleted` rather than a queued job: an attachment row is
     * only ever removed by an explicit action or by a cascade from its owner,
     * and a missing file is a smaller problem than an orphaned 60MB video that
     * nothing points at.
     */
    protected static function booted(): void
    {
        static::deleted(function (self $attachment) {
            // All three derivatives, not just the one on `file_path`. Deleting
            // only the compressed copy would leave the original and the
            // thumbnail behind — and the original is the largest of the three,
            // so that leak would be the expensive one.
            Storage::disk($attachment->disk)->delete(array_filter([
                $attachment->file_path,
                $attachment->original_path,
                $attachment->thumbnail_path,
            ]));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /** Task, TaskSubmission or TaskComment. */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TaskAssignment::class, 'task_assignment_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Storing
    |--------------------------------------------------------------------------
    */

    /**
     * Store an uploaded file against an owner and build its row.
     *
     * Classifies by MIME type and refuses anything outside the four accepted
     * categories — an allow-list, the only safe direction for a public upload
     * endpoint. Callers should still apply
     * {@see TaskAttachmentType::validationRules()} in a FormRequest so the user
     * gets a proper 422 rather than an exception.
     *
     * @param  Model  $owner  Task, TaskSubmission or TaskComment
     *
     * @throws \InvalidArgumentException when the MIME type is not accepted
     */
    public static function storeFor(
        Model $owner,
        UploadedFile $file,
        ?User $uploader = null,
        ?TaskAssignment $assignment = null,
        string $disk = 'public',
        array $clientMetadata = [],
    ): self {
        $mimeType = $file->getMimeType();
        $type = TaskAttachmentType::fromMimeType($mimeType);

        if ($type === null) {
            throw new \InvalidArgumentException(
                "File type '{$mimeType}' is not accepted. Allowed: photos, videos, documents and audio."
            );
        }

        // Foldered by type so a storage sweep can target one kind of file, and
        // named by Laravel's hash so two volunteers uploading IMG_0001.jpg on
        // the same day cannot collide.
        $directory = "task-attachments/{$type->value}";

        $base = [
            'task_id' => static::resolveTaskId($owner),
            'task_assignment_id' => $assignment?->id,
            'uploaded_by' => $uploader?->id,
            'disk' => $disk,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $type->value,
            'mime_type' => $mimeType,
        ];

        // Only photographs are processed. A video or a PDF has nothing to
        // resize and no EXIF worth reading, and pushing a 60MB video through GD
        // would exhaust memory for no benefit.
        if ($type !== TaskAttachmentType::IMAGE) {
            return $owner->attachments()->create($base + [
                'file_path' => $file->store($directory, $disk),
                'file_size' => $file->getSize(),
            ]);
        }

        $processed = app(ImageProcessingService::class)->store($file, $directory, $disk, $clientMetadata);
        $metadata = $processed['metadata'];

        return $owner->attachments()->create($base + $metadata->toColumns() + [
            // `file_path` is the compressed copy — what every screen loads.
            // The untouched bytes live in `original_path`, and the two are
            // the same file only when the upload was already small.
            'file_path' => $processed['paths']['compressed'],
            'original_path' => $processed['paths']['original'],
            'thumbnail_path' => $processed['paths']['thumbnail'],
            'file_size' => $processed['sizes']['compressed'],
            'original_size' => $processed['sizes']['original'],
            'width' => $metadata->width,
            'height' => $metadata->height,
        ]);
    }

    /**
     * Which task an owner belongs to.
     *
     * Every attachment carries this, whatever it hangs off, because it is the
     * only real foreign key on the table and therefore the only thing keeping
     * the rows from orphaning.
     *
     * @throws \InvalidArgumentException for a model that is not attachable
     */
    protected static function resolveTaskId(Model $owner): int
    {
        return match (true) {
            $owner instanceof Task => $owner->id,
            $owner instanceof TaskSubmission, $owner instanceof TaskComment => $owner->task_id,
            default => throw new \InvalidArgumentException(
                class_basename($owner).' cannot carry task attachments.'
            ),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::disk($this->disk)->url($this->file_path) : null;
    }

    public function getIsImageAttribute(): bool
    {
        return $this->file_type === TaskAttachmentType::IMAGE;
    }

    /** The small square for grids. Falls back to the viewing copy. */
    public function getThumbnailUrlAttribute(): ?string
    {
        $path = $this->thumbnail_path ?: $this->file_path;

        return $path ? Storage::disk($this->disk)->url($path) : null;
    }

    /**
     * The untouched bytes as the device sent them.
     *
     * Exposed for the review screen so a challenged submission can be examined
     * with its EXIF intact — the compressed copy has been re-encoded and no
     * longer carries it.
     */
    public function getOriginalUrlAttribute(): ?string
    {
        $path = $this->original_path ?: $this->file_path;

        return $path ? Storage::disk($this->disk)->url($path) : null;
    }

    /** Was this photograph's metadata written by the camera, or by the app? */
    public function metadataIsFromCamera(): bool
    {
        return $this->metadata_source === 'exif';
    }

    /** "Samsung SM-A155F · Android 14", or null when nothing was reported. */
    public function deviceLabel(): ?string
    {
        $parts = array_filter([
            trim(($this->device_make ?? '').' '.($this->device_model ?? '')),
            $this->device_os,
        ]);

        return $parts === [] ? null : implode(' · ', $parts);
    }

    /**
     * How much smaller the viewing copy is than the original.
     *
     * Shown in the panel so the saving is visible rather than assumed — and so
     * a compression step that quietly stopped working would be noticed.
     */
    public function compressionRatio(): ?float
    {
        if (! $this->original_size || ! $this->file_size || $this->original_size === $this->file_size) {
            return null;
        }

        return round(1 - ($this->file_size / $this->original_size), 3);
    }

    /** Human-readable size for the app's file list: "2.4 MB". */
    public function getReadableSizeAttribute(): ?string
    {
        if ($this->file_size === null) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = (float) $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, $unit === 0 ? 0 : 1).' '.$units[$unit];
    }

    /** "1:23" for the video and audio players. */
    public function getReadableDurationAttribute(): ?string
    {
        if ($this->duration_seconds === null) {
            return null;
        }

        return sprintf('%d:%02d', intdiv($this->duration_seconds, 60), $this->duration_seconds % 60);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeOfType(Builder $query, TaskAttachmentType $type): Builder
    {
        return $query->where('file_type', $type->value);
    }

    public function scopeImages(Builder $query): Builder
    {
        return $query->where('file_type', TaskAttachmentType::IMAGE->value);
    }

    /** Everything uploaded for one volunteer's job, newest first. */
    public function scopeForAssignment(Builder $query, int $assignmentId): Builder
    {
        return $query->where('task_assignment_id', $assignmentId)->latest();
    }
}
