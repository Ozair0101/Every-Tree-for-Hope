<?php

namespace App\Filament\Resources\InvolvementRequestResource\Pages;

use App\Filament\Resources\InvolvementRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvolvementRequest extends EditRecord
{
    protected static string $resource = InvolvementRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
