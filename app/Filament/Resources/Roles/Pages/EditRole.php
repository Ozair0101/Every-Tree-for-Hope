<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\Concerns\InteractsWithPermissions;
use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRole extends EditRecord
{
    use InteractsWithPermissions;

    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn (): bool => $this->record->name === \App\Providers\AuthServiceProvider::SUPER_ADMIN),
        ];
    }

    /**
     * Expand the role's current permissions into the per-group checkbox fields.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->fillPermissionGroups(
            $data,
            $this->record->permissions->pluck('name')->all(),
        );
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $permissions = $this->extractPermissionNames($data);

        $record->update($data);
        $record->syncPermissions($permissions);

        return $record;
    }
}
