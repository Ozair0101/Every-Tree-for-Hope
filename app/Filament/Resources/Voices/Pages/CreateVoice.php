<?php

namespace App\Filament\Resources\Voices\Pages;

use App\Filament\Resources\Voices\VoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVoice extends CreateRecord
{
    protected static string $resource = VoiceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
