<?php

namespace App\Filament\Resources\Donators;

use App\Filament\Resources\Donators\Pages\CreateDonator;
use App\Filament\Resources\Donators\Pages\EditDonator;
use App\Filament\Resources\Donators\Pages\ListDonators;
use App\Filament\Resources\Donators\Pages\ViewDonator;
use App\Filament\Resources\Donators\Schemas\DonatorForm;
use App\Filament\Resources\Donators\Schemas\DonatorInfolist;
use App\Filament\Resources\Donators\Tables\DonatorsTable;
use App\Models\Donator;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class DonatorResource extends Resource
{
    protected static ?string $model = Donator::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Donators';

    protected static ?string $modelLabel = 'Donator';

    protected static ?string $pluralModelLabel = 'Donators';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function form(Schema $schema): Schema
    {
        return DonatorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DonatorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonatorsTable::configure($table);
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
            'index' => ListDonators::route('/'),
            'create' => CreateDonator::route('/create'),
            'view' => ViewDonator::route('/{record}'),
            'edit' => EditDonator::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'email', 'location'];
    }

    public static function getRedirectUrl(): string
    {
        return static::getUrl('index');
    }
}
