<?php

namespace App\Filament\Resources\JobCategories;

use App\Filament\Resources\JobCategories\Pages\CreateJobCategory;
use App\Filament\Resources\JobCategories\Pages\EditJobCategory;
use App\Filament\Resources\JobCategories\Pages\ListJobCategories;
use App\Models\JobCategory;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class JobCategoryResource extends Resource
{
    protected static ?string $model = JobCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Job Categories';

    protected static string|\UnitEnum|null $navigationGroup = 'Careers';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Job Category';

    protected static ?string $pluralModelLabel = 'Job Categories';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Translations')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('English')
                            ->badge('default')
                            ->schema([
                                Components\TextInput::make('name.en')
                                    ->label('Name (English)')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g. Information Technology')
                                    ->formatStateUsing(fn ($record) => $record?->getTranslation('name', 'en', false))
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Dari (دری)')
                            ->schema([
                                Components\TextInput::make('name.fa')
                                    ->label('نام (دری)')
                                    ->maxLength(255)
                                    ->extraAttributes(['dir' => 'rtl'])
                                    ->formatStateUsing(fn ($record) => $record?->getTranslation('name', 'fa', false))
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Pashto (پښتو)')
                            ->schema([
                                Components\TextInput::make('name.ps')
                                    ->label('نوم (پښتو)')
                                    ->maxLength(255)
                                    ->extraAttributes(['dir' => 'rtl'])
                                    ->formatStateUsing(fn ($record) => $record?->getTranslation('name', 'ps', false))
                                    ->columnSpanFull(),
                            ]),
                    ]),
                Components\TextInput::make('icon')
                    ->label('Icon (Material Symbol name)')
                    ->placeholder('e.g. computer, construction, health_and_safety')
                    ->helperText('Optional. Name from Google Material Symbols.'),
                Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first.'),
                Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icon')
                    ->formatStateUsing(fn ($state) => $state ?: '—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(query: fn ($query, string $search) => $query->where('name', 'like', "%{$search}%"))
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('jobs_count')
                    ->counts('jobs')
                    ->label('Jobs')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobCategories::route('/'),
            'create' => CreateJobCategory::route('/create'),
            'edit' => EditJobCategory::route('/{record}/edit'),
        ];
    }
}
