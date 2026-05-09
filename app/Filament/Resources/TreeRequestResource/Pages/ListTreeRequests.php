<?php

namespace App\Filament\Resources\TreeRequestResource\Pages;

use App\Filament\Resources\TreeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTreeRequests extends ListRecords
{
    protected static string $resource = TreeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
