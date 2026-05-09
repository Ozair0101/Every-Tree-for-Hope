<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TreeRequestResource\Pages;
use App\Filament\Resources\TreeRequests\Schemas\TreeRequestInfolist;
use App\Models\TreeRequest;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TreeRequestResource extends Resource
{
    protected static ?string $model = TreeRequest::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Tree Requests';

    protected static ?string $modelLabel = 'Tree Request';

    protected static ?string $pluralModelLabel = 'Tree Requests';

    protected static string|\UnitEnum|null $navigationGroup = 'User Engagement';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\TextInput::make('location')
                    ->required()
                    ->maxLength(255),

                Components\TextInput::make('number_of_trees')
                    ->label('Number of Trees')
                    ->required()
                    ->numeric()
                    ->minValue(1),

                Components\TextInput::make('water_source')
                    ->label('Water Source')
                    ->maxLength(255),

                Components\TextInput::make('responsible_person')
                    ->label('Responsible Person')
                    ->required()
                    ->maxLength(255),

                Components\TextInput::make('phone_whatsapp')
                    ->label('Phone / WhatsApp')
                    ->required()
                    ->maxLength(50),

                Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewing' => 'Under Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                    ])
                    ->default('pending')
                    ->required(),

                Components\Textarea::make('admin_notes')
                    ->label('Admin Notes')
                    ->rows(3),

                Components\DateTimePicker::make('reviewed_at')
                    ->label('Reviewed At')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->location),

                Tables\Columns\TextColumn::make('number_of_trees')
                    ->label('Trees')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('responsible_person')
                    ->label('Contact Person')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone_whatsapp')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('water_source')
                    ->label('Water Source')
                    ->limit(30)
                    ->toggleable()
                    ->placeholder('Not specified'),

                Tables\Columns\IconColumn::make('has_media')
                    ->label('Media')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->media_paths))
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'reviewing',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'info' => 'completed',
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'reviewing' => 'Reviewing',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                        default => $state ?? 'Pending',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewing' => 'Under Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                    ]),

                Tables\Filters\Filter::make('has_media')
                    ->label('Has Media Files')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('media_paths')->where('media_paths', '!=', '[]')),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\BulkAction::make('markReviewing')
                        ->label('Mark as Reviewing')
                        ->icon('heroicon-o-eye')
                        ->action(function ($records) {
                            $records->each->markAsReviewing();
                        })
                        ->deselectRecordsAfterCompletion(),
                    Actions\BulkAction::make('markApproved')
                        ->label('Mark as Approved')
                        ->icon('heroicon-o-check-circle')
                        ->action(function ($records) {
                            $records->each->markAsApproved();
                        })
                        ->deselectRecordsAfterCompletion(),
                    Actions\BulkAction::make('markRejected')
                        ->label('Mark as Rejected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each->markAsRejected();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return TreeRequestInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTreeRequests::route('/'),
            'create' => Pages\CreateTreeRequest::route('/create'),
            'view' => Pages\ViewTreeRequest::route('/{record}'),
            'edit' => Pages\EditTreeRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
