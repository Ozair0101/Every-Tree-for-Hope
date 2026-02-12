<?php

namespace App\Filament\Resources\Donators\Pages;

use App\Filament\Resources\Donators\DonatorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDonator extends CreateRecord
{
    protected static string $resource = DonatorResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
