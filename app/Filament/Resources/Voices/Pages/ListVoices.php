<?php

namespace App\Filament\Resources\Voices\Pages;

use App\Filament\Resources\Voices\VoiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVoices extends ListRecords
{
    protected static string $resource = VoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
