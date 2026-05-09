<?php

namespace App\Filament\Resources\TreeRequestResource\Pages;

use App\Filament\Resources\TreeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTreeRequest extends EditRecord
{
    protected static string $resource = TreeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
