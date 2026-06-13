<?php

namespace App\Filament\Resources\SponsorPackages;

use App\Filament\Resources\SponsorPackages\Pages\CreateSponsorPackage;
use App\Filament\Resources\SponsorPackages\Pages\EditSponsorPackage;
use App\Filament\Resources\SponsorPackages\Pages\ListSponsorPackages;
use App\Models\SponsorPackage;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SponsorPackageResource extends Resource
{
    protected static ?string $model = SponsorPackage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'Sponsor Packages';

    protected static string|\UnitEnum|null $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Sponsor Package';

    protected static ?string $pluralModelLabel = 'Sponsor Packages';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Package Details')
                    ->icon('heroicon-o-gift')
                    ->columnSpanFull()
                    ->schema([
                        Components\TextInput::make('title')
                            ->label('Package Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Standard Sponsorship')
                            ->columnSpanFull(),
                        Components\TextInput::make('price')
                            ->label('Price')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->prefix(fn (callable $get) => $get('currency') === 'USD' ? '$' : null)
                            ->placeholder('100')
                            ->columnSpan(1),
                        Components\Select::make('currency')
                            ->label('Currency')
                            ->options([
                                'USD' => 'USD ($)',
                                'EUR' => 'EUR (€)',
                                'AFN' => 'AFN (؋)',
                            ])
                            ->default('USD')
                            ->required()
                            ->columnSpan(1),
                        Components\TextInput::make('trees_count')
                            ->label('Trees')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('30')
                            ->helperText('Number of trees this package sponsors')
                            ->columnSpan(1),
                        Components\TextInput::make('badge_label')
                            ->label('Badge Label')
                            ->maxLength(40)
                            ->placeholder('e.g. Most Popular')
                            ->helperText('Optional small ribbon shown on the card')
                            ->columnSpan(1),
                        Components\Textarea::make('description')
                            ->label('Short Description')
                            ->rows(3)
                            ->placeholder('One or two lines describing what the sponsor gets / supports.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Budget Allocations')
                    ->description('How the sponsorship is spent. The percentages should add up to 100%.')
                    ->icon('heroicon-o-chart-pie')
                    ->columnSpanFull()
                    ->schema([
                        Components\Repeater::make('allocations')
                            ->label('')
                            ->schema([
                                Components\TextInput::make('label')
                                    ->label('Label')
                                    ->required()
                                    ->maxLength(120)
                                    ->placeholder('e.g. Trees + Transport')
                                    ->columnSpan(2),
                                Components\TextInput::make('percentage')
                                    ->label('Percentage')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%')
                                    ->columnSpan(1),
                                Components\Select::make('tone')
                                    ->label('Color')
                                    ->options([
                                        'green' => 'Deep Green',
                                        'lime' => 'Vibrant Lime',
                                        'gold' => 'Gold Accent',
                                        'charcoal' => 'Charcoal',
                                    ])
                                    ->default('green')
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->itemLabel(fn (array $state): ?string =>
                                ($state['label'] ?? null)
                                    ? $state['label'] . ' — ' . ($state['percentage'] ?? '0') . '%'
                                    : 'New allocation'
                            )
                            ->addActionLabel('Add allocation')
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),

                Section::make('Visibility')
                    ->icon('heroicon-o-eye')
                    ->columnSpanFull()
                    ->schema([
                        Components\Toggle::make('is_featured')
                            ->label('Featured')
                            ->helperText('Visually highlighted on the page')
                            ->columnSpan(1),
                        Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive packages are hidden from the website')
                            ->columnSpan(1),
                        Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first')
                            ->columnSpan(1),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('price')
                    ->money(fn ($record) => $record->currency ?? 'USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('trees_count')
                    ->label('Trees')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('badge_label')
                    ->label('Badge')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->toggleable(),
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
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSponsorPackages::route('/'),
            'create' => CreateSponsorPackage::route('/create'),
            'edit' => EditSponsorPackage::route('/{record}/edit'),
        ];
    }
}
