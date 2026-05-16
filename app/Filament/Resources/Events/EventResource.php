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
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Events';

    protected static string|\UnitEnum|null $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 1;

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
                Components\Select::make('event_type')
                    ->label('Event Type')
                    ->required()
                    ->options([
                        'Planting' => 'Planting',
                        'Care & Watering' => 'Care & Watering',
                        'Planting (OaK)' => 'Planting (OaK)',
                        'Planting & trimming' => 'Planting & trimming',
                        'Advertisements' => 'Advertisements',
                    ])
                    ->searchable()
                    ->placeholder('Select event type')
                    ->columnSpanFull(),
                Components\Textarea::make('description')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                Components\TextInput::make('location')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Plain location name, e.g. "Kabul — Main Street"')
                    ->columnSpanFull(),
                Components\Textarea::make('map_embed')
                    ->label('Google Map (optional)')
                    ->rows(3)
                    ->placeholder('Paste the Google Maps "Embed a map" iframe, a share link, or "lat,lng" coordinates')
                    ->helperText('On Google Maps: Share → "Embed a map" → copy the HTML and paste it here. A share link (maps.app.goo.gl / google.com/maps) or "34.5553,69.2075" coordinates also work. Leave empty to skip the map.')
                    ->columnSpanFull(),
                Components\Select::make('province')
                    ->label('Province')
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
                    ->required()
                    ->helperText('Select the province where the event took place')
                    ->columnSpanFull(),
                Components\CheckboxList::make('tree_names')
                    ->label('Tree Species Planted')
                    ->options([
                        'Almond' => 'Almond (Badam)',
                        'Pine' => 'Pine (Khar)',
                        'Pomegranate' => 'Pomegranate (Anar)',
                        'Walnut' => 'Walnut (Ghaz)',
                        'Apricot' => 'Apricot (Zardalu)',
                        'Mulberry' => 'Mulberry (Toot)',
                        'Apple' => 'Apple (Sib)',
                        'Grape' => 'Grape (Angur)',
                        'Pistachio' => 'Pistachio (Pista)',
                        'Fig' => 'Fig (Anjeer)',
                        'Olive' => 'Olive (Zaytun)',
                        'Cherry' => 'Cherry (Gelas)',
                        'Plum' => 'Plum (Aloo Bukhara)',
                        'Pear' => 'Pear (Nashpati)',
                        'Chenar' => 'Chenar',
                        'Sepidar' => 'Sepidar',
                        'Acacia' => 'Acacia',
                        'Narvan / Naroon' => 'Narvan / Naroon',
                        'Kaj' => 'Kaj',
                        'Sarv' => 'Sarv',
                        'Najo' => 'Najo',
                        'Toot' => 'Toot',
                        'Anjir' => 'Anjir',
                        'Divar Bidar' => 'Divar Bidar',
                        'Arghavan' => 'Arghavan',
                        'Juniper' => 'Juniper'
                    ])
                    ->columns(3)
                    ->helperText('Select all tree species planted in this event from the list above. Do not enter the same species in the custom field below.')
                    ->columnSpanFull(),
                Components\TextInput::make('custom_tree_species')
                    ->label('Custom Tree Species')
                    ->placeholder('Enter additional tree species (comma-separated)')
                    ->helperText('Add any tree species NOT listed above. Do not repeat species already selected above.')
                    ->formatStateUsing(function ($state) {
                        if ($state) {
                            $species = array_map('trim', explode(',', $state));
                            $species = array_filter($species, function($species) {
                                return !empty($species);
                            });
                            return implode(', ', array_unique($species));
                        }
                        return $state;
                    })
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

                // ── Maintenance & Health ──
                Section::make('Maintenance & Health')
                    ->description('Track follow-up visits, tree survival, and current state of the planting site.')
                    ->icon('heroicon-o-heart')
                    ->columnSpanFull()
                    ->schema([
                        Components\TextInput::make('trees_lost')
                            ->label('Trees Lost')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Cumulative count of trees that did not survive')
                            ->columnSpan(1),
                        Components\DatePicker::make('last_maintained_at')
                            ->label('Last Maintenance Date')
                            ->native(false)
                            ->helperText('When the most recent visit took place')
                            ->columnSpan(1),
                        Components\Textarea::make('maintenance_notes')
                            ->label('Maintenance Summary')
                            ->rows(3)
                            ->placeholder('e.g. Most saplings doing well after winter; replacement planned for spring.')
                            ->helperText('Brief narrative shown on the public event page')
                            ->columnSpanFull(),
                        Components\Repeater::make('maintenance_visits')
                            ->label('Maintenance Visits (timeline)')
                            ->schema([
                                Components\DatePicker::make('date')
                                    ->label('Visit Date')
                                    ->native(false)
                                    ->required()
                                    ->columnSpan(1),
                                Components\Select::make('action')
                                    ->label('Action')
                                    ->options([
                                        'watering' => 'Watering',
                                        'pruning' => 'Pruning',
                                        'inspection' => 'Inspection',
                                        'replanting' => 'Replanting',
                                        'fencing' => 'Fencing / Protection',
                                        'fertilizing' => 'Fertilizing',
                                        'pest_control' => 'Pest Control',
                                        'other' => 'Other',
                                    ])
                                    ->required()
                                    ->columnSpan(1),
                                Components\TextInput::make('trees_lost_this_visit')
                                    ->label('Trees Lost (this visit)')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->columnSpan(1),
                                Components\Textarea::make('notes')
                                    ->label('Notes')
                                    ->rows(2)
                                    ->placeholder('What did the team observe or do?')
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                ($state['date'] ?? null)
                                    ? (string) $state['date'] . ' · ' . ($state['action'] ?? 'visit')
                                    : 'New visit'
                            )
                            ->addActionLabel('Add a maintenance visit')
                            ->defaultItems(0)
                            ->columnSpanFull(),
                        Components\FileUpload::make('maintenance_photos')
                            ->label('Current-State Photos')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->directory('event-maintenance')
                            ->disk('public')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxFiles(8)
                            ->helperText('Photos of the trees today — shown on the public event page')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Components\TextInput::make('volunteers')
                    ->label('Number of Volunteers')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->helperText('Total number of volunteers participated')
                    ->columnSpanFull(),
                Components\Repeater::make('volunteer_names')
                    ->label('Volunteer Names')
                    ->schema([
                        Components\TextInput::make('name')
                            ->label('Volunteer Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'New volunteer')
                    ->addActionLabel('Add a volunteer')
                    ->defaultItems(0)
                    ->collapsible()
                    ->reorderable()
                    ->helperText('Add one entry per volunteer. You can add as many as you like.')
                    ->columnSpanFull(),
                Components\TextInput::make('sponsor_partner')
                    ->label('Sponsor/Partner')
                    ->maxLength(255)
                    ->helperText('Name of sponsoring organization or partner')
                    ->columnSpanFull(),
                Components\TextInput::make('video_url')
                    ->label('Video URL')
                    ->url()
                    ->placeholder('https://youtube.com/watch?v=... or https://vimeo.com/...')
                    ->helperText('Optional: Link to event video')
                    ->columnSpanFull(),
                Components\Repeater::make('images')
                    ->label('Event Images')
                    ->relationship()
                    ->schema([
                        Components\FileUpload::make('image_path')
                            ->label('Image')
                            ->required()
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
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
                Tables\Columns\TextColumn::make('province')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tree_names')
                ->label('Tree Species')
                ->formatStateUsing(function ($state) {

                    if (is_array($state)) {
                        return implode(', ', $state);
                    }

                    return $state ?? 'N/A';
                })
                    ->toggleable(),
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
            ->defaultSort('created_at', 'desc');
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
