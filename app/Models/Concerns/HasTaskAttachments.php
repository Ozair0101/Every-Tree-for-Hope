<?php

namespace App\Models\Concerns;

use App\Models\TaskAttachment;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Gives a model polymorphic task attachments, and cleans them up on deletion.
 *
 * The cleanup is the reason this is a trait rather than a copy-pasted relation.
 * A polymorphic relation has no foreign key, so the database cannot cascade it:
 * deleting a TaskSubmission would leave its attachment rows pointing at nothing
 * and its files sitting on disk forever. Only the application can close that
 * gap, and every owner has to do it the same way — so it lives here and is used
 * by Task, TaskSubmission and TaskComment alike.
 *
 * Attachments are removed one model at a time rather than with a mass
 * `delete()` query, because each row's `deleted` hook is what unlinks the
 * physical file. A bulk delete would clear the table and leak the storage.
 */
trait HasTaskAttachments
{
    public static function bootHasTaskAttachments(): void
    {
        static::deleting(function ($model) {
            // Soft deletes must not destroy files — the record can still come
            // back. Only a hard delete takes the attachments with it.
            if (in_array(SoftDeletes::class, class_uses_recursive($model), true)
                && ! $model->isForceDeleting()) {
                return;
            }

            $model->purgeTaskAttachments();
        });
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(TaskAttachment::class, 'attachable')->latest();
    }

    /**
     * Remove the attachments this model is responsible for.
     *
     * Overridden by {@see \App\Models\Task}, which owns not only its own files
     * but every file hanging off its submissions and comments — those rows are
     * cascaded away by the database, which never fires an Eloquent event, so
     * their files must be unlinked here first.
     */
    protected function purgeTaskAttachments(): void
    {
        $this->attachments()->each(fn (TaskAttachment $attachment) => $attachment->delete());
    }
}
