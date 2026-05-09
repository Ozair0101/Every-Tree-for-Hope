<?php

namespace App\Filament\Resources\InvolvementRequestResource\Pages;

use App\Filament\Resources\InvolvementRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInvolvementRequest extends ViewRecord
{
    protected static string $resource = InvolvementRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
