<?php

namespace App\Filament\Resources\Trees;

use App\Filament\Resources\Trees\Pages\CreateTree;
use App\Filament\Resources\Trees\Pages\EditTree;
use App\Filament\Resources\Trees\Pages\ListTrees;
use App\Models\Tree;
use App\Notifications\TreeReviewed;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TreeResource extends Resource
{
    protected static ?string $model = Tree::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Planted Trees';

    protected static string|\UnitEnum|null $navigationGroup = 'Community';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Planted Tree';

    /** Badge with the number of trees awaiting review. */
    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('status', 'pending')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Planter')
                    ->icon('heroicon-o-user')
                    ->columnSpanFull()
                    ->schema([
                        Components\Select::make('user_id')
                            ->label('Planted by')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                Section::make('Tree')
                    ->icon('heroicon-o-sparkles')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Components\TextInput::make('species')->required()->maxLength(160),
                        Components\DatePicker::make('planted_on')->required()->maxDate(now()),
                        Components\Textarea::make('notes')->rows(4)->columnSpanFull(),
                        Components\FileUpload::make('image_path')
                            ->label('Photo')
                            ->image()
                            ->directory('trees')
                            ->disk('public')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),

                Section::make('Location')
                    ->icon('heroicon-o-globe-alt')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        Components\TextInput::make('location_name')->maxLength(200)->columnSpanFull(),
                        Components\TextInput::make('latitude')->numeric()->required(),
                        Components\TextInput::make('longitude')->numeric()->required(),
                        Components\TextInput::make('gps_accuracy')->numeric()->label('GPS accuracy (m)'),
                    ]),

                Section::make('Moderation')
                    ->icon('heroicon-o-shield-check')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending review',
                                'approved' => 'Approved (public)',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            // Approved by default, because reaching this form
                            // already required the permission that lets you
                            // approve someone else's tree. Queueing your own
                            // entry for a review only you can perform is a step
                            // with one possible outcome. Still a Select, so a
                            // record entered on someone's behalf can be held
                            // back deliberately.
                            ->default('approved'),
                        Components\TextInput::make('rejection_reason')
                            ->maxLength(255)
                            ->helperText('Shown to the planter when a tree is rejected.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('')
                    ->disk('public')
                    ->square()
                    ->defaultImageUrl(asset('favicon.ico')),
                Tables\Columns\TextColumn::make('species')
                    ->searchable()
                    ->limit(30)
                    ->description(fn (Tree $r) => $r->user?->name),
                Tables\Columns\TextColumn::make('location_name')
                    ->label('Location')
                    ->placeholder('—')
                    ->description(fn (Tree $r) => round((float) $r->latitude, 4).', '.round((float) $r->longitude, 4)),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('updates_count')
                    ->counts('updates')
                    ->label('Updates')
                    ->sortable(),
                Tables\Columns\TextColumn::make('planted_on')->date()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->authorize('approve_tree')
                    ->visible(fn (Tree $r) => $r->status !== 'approved')
                    ->action(function (Tree $r) {
                        $r->update(['status' => 'approved', 'approved_at' => now(), 'rejection_reason' => null]);
                        $r->user?->notify(new TreeReviewed($r, 'approved'));
                    }),
                Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->authorize('reject_tree')
                    ->visible(fn (Tree $r) => $r->status !== 'rejected')
                    ->action(function (Tree $r) {
                        $r->update(['status' => 'rejected']);
                        $r->user?->notify(new TreeReviewed($r, 'rejected'));
                    }),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\BulkAction::make('approve_selected')
                        ->label('Approve selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->authorize('approve_tree')
                        ->action(fn ($records) => $records->each(function (Tree $r) {
                            $r->update(['status' => 'approved', 'approved_at' => now(), 'rejection_reason' => null]);
                            $r->user?->notify(new TreeReviewed($r, 'approved'));
                        })),
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrees::route('/'),
            'create' => CreateTree::route('/create'),
            'edit' => EditTree::route('/{record}/edit'),
        ];
    }
}
