<?php

namespace App\Services\Tasks;

use App\Models\TaskAssignment;
use App\Models\TaskAttachment;
use App\Models\TaskProgress;
use App\Models\User;
use App\Services\Tasks\Exceptions\TaskOperationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Interim progress reports from the field.
 *
 * Thin by design — `TaskAssignment::reportProgress()` already handles the
 * idempotency key, the auto-start and the roll-up. What belongs here is the
 * guard and the optional photos, so a controller never has to know that a
 * progress report can carry attachments.
 */
class TaskProgressService
{
    /**
     * @param  array<int, UploadedFile>  $files
     */
    public function report(
        TaskAssignment $assignment,
        User $actor,
        int $percentage,
        ?string $note = null,
        ?float $latitude = null,
        ?float $longitude = null,
        array $files = [],
        ?string $clientUuid = null,
    ): TaskProgress {
        // A finished or handed-on assignment has nothing left to report against;
        // accepting one would move the roll-up for work nobody is doing.
        if ($assignment->status->isTerminal()) {
            throw TaskOperationException::assignmentClosed($assignment);
        }

        if (! $assignment->role->canSubmit()) {
            throw TaskOperationException::notAssigned($assignment->task);
        }

        return DB::transaction(function () use ($assignment, $actor, $percentage, $note, $latitude, $longitude, $files, $clientUuid) {
            $progress = $assignment->reportProgress(
                percentage: $percentage,
                actor: $actor,
                note: $note,
                latitude: $latitude,
                longitude: $longitude,
                clientUuid: $clientUuid,
            );

            // Photos hang off the submission-shaped owner chain via the task, so
            // a progress photo shows up in the same gallery as everything else.
            foreach ($files as $file) {
                TaskAttachment::storeFor($assignment->task, $file, $actor, $assignment);
            }

            return $progress;
        });
    }
}
