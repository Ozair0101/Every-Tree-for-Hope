<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Enums\TaskAssignmentRole;
use App\Filament\Resources\Tasks\TaskResource;
use App\Services\Tasks\TaskAssignmentService;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    /** Show the current team in the picker. */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['assignee_ids'] = $this->record->assignees()->pluck('user_id')->all();

        return $data;
    }

    /**
     * Reconcile the team with what the picker now holds.
     *
     * Only the difference is touched. Re-assigning everyone on every save would
     * re-notify people who were already on the task and reset the personal
     * clocks (accepted, started) that TaskAssignment keeps per user.
     */
    protected function afterSave(): void
    {
        $selected = array_map('intval', array_filter((array) ($this->data['assignee_ids'] ?? [])));
        $current = $this->record->assignees()->pluck('user_id')->map('intval')->all();

        $added = array_values(array_diff($selected, $current));
        $removed = array_values(array_diff($current, $selected));

        try {
            if ($added !== []) {
                app(TaskAssignmentService::class)->assign($this->record, $added, auth()->user());
            }

            if ($removed !== []) {
                // Deleted outright rather than marked declined: a coordinator
                // removing someone from the picker is correcting the roster,
                // not recording that the volunteer turned the work down.
                $this->record->assignments()
                    ->whereIn('user_id', $removed)
                    ->where('role', TaskAssignmentRole::ASSIGNEE->value)
                    ->delete();
            }
        } catch (\Throwable $e) {
            report($e);

            Notification::make()
                ->title('Task saved, but the team was not fully updated')
                ->body($e->getMessage())
                ->warning()
                ->persistent()
                ->send();
        }
    }
}
