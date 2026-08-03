<?php

namespace App\Filament\Resources\Trees\RelationManagers;

use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * The progress log of a planted tree, shown to admins on the tree's edit page.
 *
 * Read-only on purpose: progress entries are the planter's field record — their
 * note, the height they measured, the photos they took on the day. An admin
 * reviews that history; they do not author it. (Removing a bad entry is a
 * moderation concern handled on the tree itself.)
 */
class UpdatesRelationManager extends RelationManager
{
    protected static string $relationship = 'updates';

    protected static ?string $title = 'Progress log';

    // Must match RelationManager::$icon exactly — PHP rejects a narrower type
    // on a redeclared static property, and the fatal it raises happens at class
    // load, taking the whole app down rather than just this panel.
    protected static string|BackedEnum|null $icon = 'heroicon-o-arrow-trending-up';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('note')
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Photo')
                    ->square()
                    ->size(56),

                Tables\Columns\TextColumn::make('images_count')
                    ->counts('images')
                    ->label('Photos')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('note')
                    ->label('Update')
                    ->wrap()
                    ->limit(120),

                Tables\Columns\TextColumn::make('height_cm')
                    ->label('Height')
                    ->formatStateUsing(fn ($state) => $state !== null ? $state.' cm' : '—')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Logged')
                    ->dateTime('M j, Y · H:i')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            // No create/edit/delete: this is the planter's record to read, not
            // for staff to write.
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
