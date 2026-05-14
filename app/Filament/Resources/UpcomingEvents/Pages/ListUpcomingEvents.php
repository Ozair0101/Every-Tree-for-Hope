<?php

namespace App\Filament\Resources\UpcomingEvents\Pages;

use App\Filament\Resources\UpcomingEvents\UpcomingEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUpcomingEvents extends ListRecords
{
    protected static string $resource = UpcomingEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
