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
use Filament\Schemas\Components\Tabs;
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
                Tabs::make('Translations')
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tabs\Tab::make('English')
                            ->badge('default')
                            ->schema([
                                Components\TextInput::make('title.en')
                                    ->label('Title (English)')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g. Spring Planting at Pul-i-Bagh')
                                    ->formatStateUsing(fn ($record) => $record?->getTranslation('title', 'en', false))
                                    ->columnSpanFull(),
                                Components\Textarea::make('description.en')
                                    ->label('Description (English)')
                                    ->required()
                                    ->rows(5)
                                    ->helperText('Shown by default. Visitors see this when their language has no translation.')
                                    ->formatStateUsing(fn ($record) => $record?->getTranslation('description', 'en', false))
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Dari (دری)')
                            ->schema([
                                Components\TextInput::make('title.fa')
                                    ->label('عنوان (دری)')
                                    ->maxLength(255)
                                    ->placeholder('مثلاً نهال‌شانی بهاری در پل باغ')
                                    ->extraAttributes(['dir' => 'rtl'])
                                    ->formatStateUsing(fn ($record) => $record?->getTranslation('title', 'fa', false))
                                    ->columnSpanFull(),
                                Components\Textarea::make('description.fa')
                                    ->label('توضیحات (دری)')
                                    ->rows(5)
                                    ->extraAttributes(['dir' => 'rtl'])
                                    ->helperText('در صورت خالی بودن، نسخهٔ انگلیسی نمایش داده می‌شود.')
                                    ->formatStateUsing(fn ($record) => $record?->getTranslation('description', 'fa', false))
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Pashto (پښتو)')
                            ->schema([
                                Components\TextInput::make('title.ps')
                                    ->label('سرلیک (پښتو)')
                                    ->maxLength(255)
                                    ->placeholder('بېلګه: د پسرلي ونه کرل په پل باغ کې')
                                    ->extraAttributes(['dir' => 'rtl'])
                                    ->formatStateUsing(fn ($record) => $record?->getTranslation('title', 'ps', false))
                                    ->columnSpanFull(),
                                Components\Textarea::make('description.ps')
                                    ->label('تفصیل (پښتو)')
                                    ->rows(5)
                                    ->extraAttributes(['dir' => 'rtl'])
                                    ->helperText('که خالي وي، انګلیسي نسخه ښودل کیږي.')
                                    ->formatStateUsing(fn ($record) => $record?->getTranslation('description', 'ps', false))
                                    ->columnSpanFull(),
                            ]),
                    ]),
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
