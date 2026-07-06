<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Support\PermissionCatalog;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Access Control';

    protected static ?string $navigationLabel = 'Roles';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Build the state-path key for a permission group's checkbox list.
     * e.g. "User Engagement" -> "permissions_user_engagement".
     */
    public static function groupField(string $group): string
    {
        return 'permissions_'.Str::slug($group, '_');
    }

    public static function form(Schema $schema): Schema
    {
        $permissionSections = [];

        foreach (PermissionCatalog::groupedForUi() as $group => $permissions) {
            $permissionSections[] = Section::make($group)
                ->description('Grant abilities for the '.$group.' area.')
                ->icon('heroicon-o-key')
                ->collapsible()
                ->collapsed()
                ->columnSpanFull()
                ->schema([
                    CheckboxList::make(self::groupField($group))
                        ->hiddenLabel()
                        // Option keys ARE the permission names; labels are human-readable.
                        ->options($permissions)
                        ->columns(3)
                        ->gridDirection('row')
                        ->bulkToggleable()
                        ->searchable(),
                ]);
        }

        return $schema->components([
            Section::make('Role details')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(125)
                        ->unique(ignoreRecord: true)
                        ->helperText('e.g. "Content Editor". Changing this renames the role everywhere.'),
                    Hidden::make('guard_name')->default('web'),
                ]),
            ...$permissionSections,
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    // Never allow deleting the Super Admin role — the Gate::before
                    // bypass depends on it existing.
                    ->hidden(fn (Role $record): bool => $record->name === \App\Providers\AuthServiceProvider::SUPER_ADMIN),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
