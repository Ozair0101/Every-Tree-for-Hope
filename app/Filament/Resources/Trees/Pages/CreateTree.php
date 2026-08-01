<?php

namespace App\Filament\Resources\Trees\Pages;

use App\Filament\Resources\Trees\TreeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTree extends CreateRecord
{
    protected static string $resource = TreeResource::class;

    /**
     * A tree recorded here is already approved.
     *
     * Moderation exists to check submissions from the public. Someone who
     * reached this form has already passed that check — they hold the panel
     * permission that lets them approve other people's trees — so queueing
     * their own entry for review would mean approving their own work as a
     * second step that can only ever have one outcome.
     *
     * The status field defaults to "approved" for that reason; this hook only
     * keeps the columns that travel with it consistent. A reviewer who
     * deliberately picks "pending" is respected.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] ??= 'approved';

        if ($data['status'] === 'approved') {
            $data['approved_at'] ??= now();
        }

        // Recorded by staff on the programme's behalf unless the form named
        // someone — without this the row has no planter and fails the FK.
        $data['user_id'] ??= auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
