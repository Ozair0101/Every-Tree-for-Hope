<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use App\Services\Tasks\TaskAssignmentService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    /** Stamp the author, exactly as the API does. */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    /**
     * Hand the task to the chosen volunteers.
     *
     * After creation rather than during: an assignment needs the task's id, and
     * the service also notifies each assignee — which must not fire for a task
     * whose own insert then fails.
     *
     * Routed through TaskAssignmentService rather than writing rows here, so the
     * panel and the mobile API create assignments identically: same activity
     * log, same notification, same rules.
     */
    protected function afterCreate(): void
    {
        $ids = array_filter((array) ($this->data['assignee_ids'] ?? []));

        if ($ids === []) {
            return;
        }

        try {
            app(TaskAssignmentService::class)->assign($this->record, $ids, auth()->user());
        } catch (\Throwable $e) {
            report($e);

            // The task itself saved. Say precisely what did not happen, rather
            // than implying the whole thing failed and inviting a duplicate.
            Notification::make()
                ->title('Task created, but the team was not assigned')
                ->body('Add them from the task page. '.$e->getMessage())
                ->warning()
                ->persistent()
                ->send();
        }
    }
}
