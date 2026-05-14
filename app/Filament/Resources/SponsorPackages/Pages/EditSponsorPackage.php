<?php

namespace App\Filament\Resources\SponsorPackages\Pages;

use App\Filament\Resources\SponsorPackages\SponsorPackageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSponsorPackage extends EditRecord
{
    protected static string $resource = SponsorPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
