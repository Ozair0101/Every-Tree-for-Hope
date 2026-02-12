<?php

namespace App\Filament\Resources\Donators\Pages;

use App\Filament\Resources\Donators\DonatorResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDonator extends ViewRecord
{
    protected static string $resource = DonatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
