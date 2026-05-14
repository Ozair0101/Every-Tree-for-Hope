<?php

namespace App\Filament\Resources\UpcomingEvents\Pages;

use App\Filament\Resources\UpcomingEvents\UpcomingEventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUpcomingEvent extends CreateRecord
{
    protected static string $resource = UpcomingEventResource::class;
}
