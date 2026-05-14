<?php

namespace App\Filament\Resources\SponsorPackages\Pages;

use App\Filament\Resources\SponsorPackages\SponsorPackageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSponsorPackages extends ListRecords
{
    protected static string $resource = SponsorPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
