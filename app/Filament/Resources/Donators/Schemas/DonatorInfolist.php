<?php

namespace App\Filament\Resources\Donators\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DonatorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile Information')
                    ->description('Donor personal details and photo')
                    ->schema([
                        ImageEntry::make('profile_image')
                            ->label('Profile Photo')
                            ->size(150)
                            ->circular()
                            ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name) . '&color=7F9CF5&background=EBF4FF&size=150')
                            ->columnSpanFull()
                            ->alignCenter(),
                        
                        TextEntry::make('full_name')
                            ->label('Full Name')
                            ->size('text-2xl')
                            ->weight('bold')
                            ->columnSpanFull()
                            ->alignCenter(),
                        
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->placeholder('No email provided'),
                        
                        TextEntry::make('phone')
                            ->label('Phone Number')
                            ->icon('heroicon-o-phone')
                            ->placeholder('No phone provided'),
                    ])
                    ->columns(2),

                Section::make('Location Information')
                    ->description('Donor location details')
                    ->schema([
                        TextEntry::make('location')
                            ->label('Location')
                            ->icon('heroicon-o-map-pin')
                            ->placeholder('No location provided'),
                        
                        TextEntry::make('location_type')
                            ->label('Location Type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Local' => 'primary',
                                'International' => 'success',
                                'National' => 'warning',
                                'Regional' => 'info',
                            })
                            ->placeholder('Not specified'),
                    ])
                    ->columns(2),

                Section::make('Donation Details')
                    ->description('Information about the donation and impact')
                    ->schema([
                        TextEntry::make('financial_support')
                            ->label('Financial Support')
                            ->money('USD')
                            ->icon('heroicon-o-banknotes')
                            ->placeholder('No financial support recorded'),
                        
                        TextEntry::make('trees_sponsored')
                            ->label('Trees Sponsored')
                            ->icon('heroicon-o-leaf')
                            ->badge()
                            ->color('success')
                            ->alignCenter(),
                        
                        TextEntry::make('donation_date')
                            ->label('Donation Date')
                            ->date('M d, Y')
                            ->icon('heroicon-o-calendar')
                            ->placeholder('No date recorded'),
                        
                        TextEntry::make('status')
                            ->label('Verification Status')
                            ->badge()
                            ->color(fn (string $state): string => $state === 'verified' ? 'success' : 'warning')
                            ->icon(fn (string $state): string => $state === 'verified' ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle'),
                    ])
                    ->columns(2),

                Section::make('Impact Description')
                    ->description('Environmental impact and additional notes')
                    ->schema([
                        TextEntry::make('impact')
                            ->label('Impact Description')
                            ->placeholder('No impact description provided')
                            ->columnSpanFull(),
                        
                        TextEntry::make('notes')
                            ->label('Additional Notes')
                            ->placeholder('No additional notes')
                            ->columnSpanFull(),
                    ]),

                Section::make('Website Features')
                    ->description('Website display settings')
                    ->schema([
                        IconEntry::make('is_featured')
                            ->label('Featured Donor')
                            ->boolean()
                            ->alignCenter()
                            ->size('text-2xl'),
                    ])
                    ->columns(1),

                Section::make('System Information')
                    ->description('Record timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('M d, Y H:i')
                            ->icon('heroicon-o-clock'),
                        
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('M d, Y H:i')
                            ->icon('heroicon-o-arrow-path'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
