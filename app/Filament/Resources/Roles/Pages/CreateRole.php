<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\Concerns\InteractsWithPermissions;
use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRole extends CreateRecord
{
    use InteractsWithPermissions;

    protected static string $resource = RoleResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $permissions = $this->extractPermissionNames($data);

        $data['guard_name'] ??= 'web';

        /** @var \Spatie\Permission\Models\Role $role */
        $role = static::getModel()::create($data);
        $role->syncPermissions($permissions);

        return $role;
    }
}
