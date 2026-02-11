<?php

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Models\Event;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Events';

    protected static ?string $modelLabel = 'Event';

    protected static ?string $pluralModelLabel = 'Events & Initiatives';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Components\Textarea::make('description')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                Components\TextInput::make('location')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Event location or address')
                    ->columnSpanFull(),
                Components\DatePicker::make('date')
                    ->required()
                    ->columnSpanFull(),
                Components\TextInput::make('trees_planted')
                    ->label('Trees Planted')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->helperText('Number of trees planted in this event')
                    ->columnSpanFull(),
                Components\TextInput::make('volunteers')
                    ->label('Number of Volunteers')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->helperText('Total number of volunteers participated')
                    ->columnSpanFull(),
                Components\TextInput::make('sponsor_partner')
                    ->label('Sponsor/Partner')
                    ->maxLength(255)
                    ->helperText('Name of sponsoring organization or partner')
                    ->columnSpanFull(),
                Components\Repeater::make('images')
                    ->label('Event Images')
                    ->relationship()
                    ->schema([
                        Components\FileUpload::make('image_path')
                            ->label('Image')
                            ->required()
                            ->image()
                            ->directory('event-images')
                            ->disk('public')
                            ->visibility('public')
                            ->imageEditor()
                            ->columnSpanFull(),
                        Components\TextInput::make('caption')
                            ->label('Caption')
                            ->maxLength(255)
                            ->helperText('Optional image caption')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['caption'] ?? 'Event Image')
                    ->columns(1)
                    ->columnSpanFull(),
                Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Inactive events will not be displayed on the website'),
                Components\TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('trees_planted')
                    ->label('Trees')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('volunteers')
                    ->label('Volunteers')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sponsor_partner')
                    ->label('Sponsor')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => true]);
                        })
                        ->deselectRecordsAfterCompletion(),
                    Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-mark')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => false]);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'edit' => EditEvent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
