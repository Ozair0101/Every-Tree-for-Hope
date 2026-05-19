<?php

namespace App\Filament\Resources\Donators\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DonatorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Basic Information
                TextInput::make('full_name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->maxLength(20),

                // Donation Details
                TextInput::make('financial_support')
                    ->label('Financial Support ($)')
                    ->numeric()
                    ->prefix('$')
                    ->step(0.01)
                    ->maxValue(999999.99),

                DatePicker::make('donation_date')
                    ->label('Donation Date')
                    ->native(false)
                    ->displayFormat('M d, Y'),

                Select::make('status')
                    ->label('Verification Status')
                    ->required()
                    ->options([
                        'verified' => 'Verified',
                        'unverified' => 'Unverified',
                    ])
                    ->default('unverified'),

                TextInput::make('location')
                    ->label('Location')
                    ->placeholder('e.g., Kabul, Afghanistan')
                    ->maxLength(255),

                // Shown publicly
                Textarea::make('impact')
                    ->label('Impact Description')
                    ->placeholder('Describe the environmental impact...')
                    ->rows(4)
                    ->columnSpanFull(),

                FileUpload::make('profile_image')
                    ->label('Profile Image')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->directory('donators')
                    ->disk('public')
                    ->maxSize(2048) // 2MB
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
