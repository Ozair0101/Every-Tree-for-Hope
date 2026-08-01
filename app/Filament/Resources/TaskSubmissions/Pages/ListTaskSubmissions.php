<?php

namespace App\Filament\Resources\TaskSubmissions\Pages;

use App\Filament\Resources\TaskSubmissions\TaskSubmissionResource;
use Filament\Resources\Pages\ListRecords;

class ListTaskSubmissions extends ListRecords
{
    protected static string $resource = TaskSubmissionResource::class;

    /** No create action: submissions come from volunteers, never from the panel. */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
