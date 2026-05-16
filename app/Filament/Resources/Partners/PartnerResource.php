<?php

namespace App\Filament\Resources\Partners;

use App\Filament\Resources\Partners\Pages\CreatePartner;
use App\Filament\Resources\Partners\Pages\EditPartner;
use App\Filament\Resources\Partners\Pages\ListPartners;
use App\Enums\PartnerType;
use App\Models\Partner;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Partners';

    protected static string|\UnitEnum|null $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Partner';

    protected static ?string $pluralModelLabel = 'Global Partners';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\TextInput::make('company_name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Components\Select::make('type')
                    ->label('Partner Type')
                    ->options(PartnerType::options())
                    ->default(PartnerType::SPONSOR->value)
                    ->required()
                    ->native(false)
                    ->helperText('Are they a sponsor, an advisor, or another kind of partner?')
                    ->columnSpanFull(),
                Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(4)
                    ->maxLength(2000)
                    ->placeholder('Briefly describe this partner and how they support the mission…')
                    ->helperText('Optional — shown on the website where partners are listed')
                    ->columnSpanFull(),
                Components\FileUpload::make('logo')
                    ->label('Company Logo')
                    ->required()
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->directory('partner-logos')
                    ->disk('public')
                    ->visibility('public')
                    ->imageEditor()
                    ->helperText('Upload the company logo (recommended: PNG with transparent background)')
                    ->columnSpanFull(),
                Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Inactive partners will not be displayed on the website'),
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
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->defaultImageUrl(url('/placeholder-logo.png')),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof PartnerType ? $state->label() : $state)
                    ->color(fn ($state) => \Filament\Support\Colors\Color::hex(
                        $state instanceof PartnerType ? $state->color() : '#6b7280'
                    ))
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(60)
                    ->toggleable()
                    ->wrap(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
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
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order');
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
            'index' => ListPartners::route('/'),
            'create' => CreatePartner::route('/create'),
            'edit' => EditPartner::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
