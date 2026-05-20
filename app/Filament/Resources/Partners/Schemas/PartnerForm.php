<?php

namespace App\Filament\Resources\Partners\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Sponsor Code')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('Generated automatically when you save')
                    ->helperText('Give this code to the partner — they can use it on the gallery page to see the events they sponsored.'),
                TextInput::make('company_name')
                    ->required(),
                TextInput::make('logo')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
