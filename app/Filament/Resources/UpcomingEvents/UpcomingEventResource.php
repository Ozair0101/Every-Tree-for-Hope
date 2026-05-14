<?php

namespace App\Filament\Resources\UpcomingEvents;

use App\Filament\Resources\UpcomingEvents\Pages\CreateUpcomingEvent;
use App\Filament\Resources\UpcomingEvents\Pages\EditUpcomingEvent;
use App\Filament\Resources\UpcomingEvents\Pages\ListUpcomingEvents;
use App\Models\UpcomingEvent;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UpcomingEventResource extends Resource
{
    protected static ?string $model = UpcomingEvent::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Upcoming Events';

    protected static string|\UnitEnum|null $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Upcoming Event';

    protected static ?string $pluralModelLabel = 'Upcoming Events';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Spring Planting at Pul-i-Bagh')
                    ->columnSpanFull(),
                Components\Textarea::make('description')
                    ->required()
                    ->rows(5)
                    ->helperText('Briefly describe what the event is about, what volunteers will do, what to bring, etc.')
                    ->columnSpanFull(),
                Components\DatePicker::make('date')
                    ->label('Event Date')
                    ->required()
                    ->native(false)
                    ->minDate(now())
                    ->helperText('Past dates are filtered out automatically on the website')
                    ->columnSpanFull(),
                Components\TextInput::make('location')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Pul-i-Bagh Park')
                    ->helperText('Specific location, park or address')
                    ->columnSpanFull(),
                Components\Select::make('province')
                    ->required()
                    ->options([
                        'Badakhshan' => 'Badakhshan',
                        'Badghis' => 'Badghis',
                        'Baghlan' => 'Baghlan',
                        'Balkh' => 'Balkh',
                        'Bamyan' => 'Bamyan',
                        'Daykundi' => 'Daykundi',
                        'Farah' => 'Farah',
                        'Faryab' => 'Faryab',
                        'Ghazni' => 'Ghazni',
                        'Ghor' => 'Ghor',
                        'Helmand' => 'Helmand',
                        'Herat' => 'Herat',
                        'Jowzjan' => 'Jowzjan',
                        'Kabul' => 'Kabul',
                        'Kandahar' => 'Kandahar',
                        'Kapisa' => 'Kapisa',
                        'Khost' => 'Khost',
                        'Kunar' => 'Kunar',
                        'Kunduz' => 'Kunduz',
                        'Laghman' => 'Laghman',
                        'Logar' => 'Logar',
                        'Maimana' => 'Maimana',
                        'Nangarhar' => 'Nangarhar',
                        'Nimruz' => 'Nimruz',
                        'Nuristan' => 'Nuristan',
                        'Paktia' => 'Paktia',
                        'Paktika' => 'Paktika',
                        'Panjshir' => 'Panjshir',
                        'Parwan' => 'Parwan',
                        'Samangan' => 'Samangan',
                        'Sar-e Pol' => 'Sar-e Pol',
                        'Takhar' => 'Takhar',
                        'Urozgan' => 'Urozgan',
                        'Wardak' => 'Wardak',
                        'Zabul' => 'Zabul',
                    ])
                    ->searchable()
                    ->columnSpanFull(),
                Components\FileUpload::make('images')
                    ->label('Event Images')
                    ->multiple()
                    ->reorderable()
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->directory('upcoming-events')
                    ->disk('public')
                    ->visibility('public')
                    ->imageEditor()
                    ->maxFiles(8)
                    ->helperText('Upload one or more images. The first image is used as the hero on the card.')
                    ->columnSpanFull(),
                Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Inactive events are hidden from the website'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->label('')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->getStateUsing(fn ($record) => is_array($record->images) ? $record->images : []),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('date')
                    ->date('M j, Y')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->date && $record->date->isFuture() ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('province')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('registrations_count')
                    ->label('Registrations')
                    ->counts('registrations')
                    ->badge()
                    ->color('primary'),
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
                Tables\Filters\Filter::make('upcoming')
                    ->label('Upcoming only')
                    ->query(fn (Builder $query) => $query->whereDate('date', '>=', now()->toDateString())),
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
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-mark')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('date', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUpcomingEvents::route('/'),
            'create' => CreateUpcomingEvent::route('/create'),
            'edit' => EditUpcomingEvent::route('/{record}/edit'),
        ];
    }
}
