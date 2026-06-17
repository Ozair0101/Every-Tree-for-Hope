<?php

namespace App\Filament\Resources\Voices\Pages;

use App\Filament\Resources\Voices\VoiceCommentResource;
use Filament\Resources\Pages\ListRecords;

class ListVoiceComments extends ListRecords
{
    protected static string $resource = VoiceCommentResource::class;
}
