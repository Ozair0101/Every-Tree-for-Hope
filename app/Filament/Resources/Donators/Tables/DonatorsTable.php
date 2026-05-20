<?php

namespace App\Filament\Resources\Donators\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DonatorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_image')
                    ->label('Photo')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name) . '&color=7F9CF5&background=EBF4FF')
                    ->size(40),

                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->grow(true),

                TextColumn::make('code')
                    ->label('Sponsor Code')
                    ->badge()
                    ->color('warning')
                    ->copyable()
                    ->copyMessage('Sponsor code copied')
                    ->searchable(),

                TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-map-pin'),

                TextColumn::make('financial_support')
                    ->label('Support')
                    ->money('USD')
                    ->sortable()
                    ->alignCenter()
                    ->weight('bold'),

                IconColumn::make('status')
                    ->label('Status')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-exclamation-circle')
                    ->sortable(),

                TextColumn::make('donation_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Verification Status')
                    ->options([
                        'verified' => 'Verified',
                        'unverified' => 'Unverified',
                    ]),
            ])
            ->actions([
                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('primary'),
                
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),
                
                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateHeading('No donators found')
            ->emptyStateDescription('Create your first donator to get started.')
            ->emptyStateActions([
                CreateAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
