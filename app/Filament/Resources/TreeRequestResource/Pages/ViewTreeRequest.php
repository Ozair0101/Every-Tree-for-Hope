<?php

namespace App\Filament\Resources\TreeRequestResource\Pages;

use App\Filament\Resources\TreeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTreeRequest extends ViewRecord
{
    protected static string $resource = TreeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
