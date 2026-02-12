<?php

namespace App\Filament\Resources\Donators\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
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
                
                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                
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
                
                TextInput::make('trees_sponsored')
                    ->label('Trees Sponsored')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                
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
                
                // Location Information
                TextInput::make('location')
                    ->label('Location')
                    ->placeholder('e.g., Kabul, Afghanistan')
                    ->maxLength(255),
                
                Select::make('location_type')
                    ->label('Location Type')
                    ->options([
                        'Local' => 'Local',
                        'International' => 'International',
                        'National' => 'National',
                        'Regional' => 'Regional',
                    ])
                    ->default('Local'),

                // Impact & Notes
                Textarea::make('impact')
                    ->label('Impact Description')
                    ->placeholder('Describe the environmental impact...')
                    ->rows(4)
                    ->columnSpanFull(),
                
                Textarea::make('notes')
                    ->label('Additional Notes')
                    ->placeholder('Any additional notes about the donor...')
                    ->rows(3)
                    ->columnSpanFull(),

                // Media & Features
                FileUpload::make('profile_image')
                    ->label('Profile Image')
                    ->image()
                    ->directory('donators')
                    ->disk('public')
                    ->maxSize(2048) // 2MB
                    ->columnSpanFull(),
                
                Toggle::make('is_featured')
                    ->label('Featured Donor')
                    ->helperText('Show this donor on the website homepage')
                    ->default(false),
            ])
            ->columns(2);
    }
}
