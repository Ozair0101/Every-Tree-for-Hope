<?php

namespace App\Filament\Resources\Donators\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DonatorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Profile Photo
                ImageEntry::make('profile_image')
                    ->label('Profile Photo')
                    ->size(150)
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name) . '&color=7F9CF5&background=EBF4FF&size=150')
                    ->columnSpanFull()
                    ->alignCenter(),
                
                // Basic Information
                TextEntry::make('full_name')
                    ->label('Full Name')
                    ->size('text-lg')
                    ->weight('bold')
                    ->columnSpanFull(),

                TextEntry::make('phone')
                    ->label('Phone Number')
                    ->placeholder('Not provided'),

                // Donation Information
                TextEntry::make('financial_support')
                    ->label('Financial Support')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 2))
                    ->weight('semibold'),

                TextEntry::make('donation_date')
                    ->label('Donation Date')
                    ->date('M d, Y')
                    ->placeholder('Not specified'),

                TextEntry::make('status')
                    ->label('Verification Status')
                    ->badge()
                    ->color(fn ($state) => $state === 'verified' ? 'success' : 'warning')
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                TextEntry::make('location')
                    ->label('Location')
                    ->placeholder('Not specified'),

                // Shown publicly
                TextEntry::make('impact')
                    ->label('Impact Description')
                    ->placeholder('No impact description provided')
                    ->columnSpanFull(),

                TextEntry::make('created_at')
                    ->label('Added On')
                    ->date('M d, Y')
                    ->placeholder('Unknown'),
            ])
            ->columns(2);
    }
}
