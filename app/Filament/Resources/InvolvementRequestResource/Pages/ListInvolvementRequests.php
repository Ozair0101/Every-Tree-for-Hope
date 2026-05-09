<?php

namespace App\Filament\Resources\InvolvementRequestResource\Pages;

use App\Filament\Resources\InvolvementRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvolvementRequests extends ListRecords
{
    protected static string $resource = InvolvementRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
