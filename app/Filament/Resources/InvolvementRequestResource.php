<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvolvementRequestResource\Pages;
use App\Models\InvolvementRequest;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvolvementRequestResource extends Resource
{
    protected static ?string $model = InvolvementRequest::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-hand-raised';

    protected static ?string $navigationLabel = 'Involvement Requests';

    protected static ?string $modelLabel = 'Involvement Request';

    protected static ?string $pluralModelLabel = 'Involvement Requests';

    protected static string|\UnitEnum|null $navigationGroup = 'User Engagement';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Select::make('type')
                    ->label('Request Type')
                    ->options([
                        'volunteer' => 'Volunteer',
                        'sponsor' => 'Sponsor',
                        'collaborate' => 'Collaborate',
                    ])
                    ->required()
                    ->disabled(),

                Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Components\TextInput::make('phone')
                    ->required()
                    ->maxLength(50),

                Components\Textarea::make('message')
                    ->required()
                    ->rows(6),

                Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewing' => 'Under Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                    ])
                    ->required()
                    ->default('pending'),

                Components\Textarea::make('admin_notes')
                    ->label('Admin Notes')
                    ->rows(3)
                    ->placeholder('Internal notes about this request...'),

                Components\DateTimePicker::make('reviewed_at')
                    ->label('Reviewed At')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'success' => 'volunteer',
                        'warning' => 'sponsor',
                        'info' => 'collaborate',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'volunteer' => 'Volunteer',
                        'sponsor' => 'Sponsor',
                        'collaborate' => 'Collaborate',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'reviewing',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'info' => 'completed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'reviewing' => 'Reviewing',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Reviewed')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Not reviewed'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'volunteer' => 'Volunteer',
                        'sponsor' => 'Sponsor',
                        'collaborate' => 'Collaborate',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewing' => 'Under Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                    ]),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvolvementRequests::route('/'),
            'create' => Pages\CreateInvolvementRequest::route('/create'),
            'view' => Pages\ViewInvolvementRequest::route('/{record}'),
            'edit' => Pages\EditInvolvementRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
