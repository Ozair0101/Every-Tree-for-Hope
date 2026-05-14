<?php

namespace App\Filament\Resources\UpcomingEvents\Pages;

use App\Filament\Resources\UpcomingEvents\UpcomingEventResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUpcomingEvent extends EditRecord
{
    protected static string $resource = UpcomingEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
