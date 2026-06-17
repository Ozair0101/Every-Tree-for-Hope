<?php

namespace App\Filament\Resources\Voices;

use App\Filament\Resources\Voices\Pages\CreateVoice;
use App\Filament\Resources\Voices\Pages\EditVoice;
use App\Filament\Resources\Voices\Pages\ListVoices;
use App\Models\Voice;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class VoiceResource extends Resource
{
    protected static ?string $model = Voice::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Voices';

    protected static string|\UnitEnum|null $navigationGroup = 'Community';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Voice';

    /**
     * Show a badge with the number of voices awaiting moderation.
     */
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
                Section::make('Author')
                    ->icon('heroicon-o-user')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        Components\TextInput::make('author_name')->required()->maxLength(120),
                        Components\TextInput::make('country')->maxLength(120),
                        Components\TextInput::make('author_email')
                            ->email()
                            ->maxLength(255)
                            ->helperText('Private — never shown publicly.'),
                    ]),

                Section::make('Content')
                    ->icon('heroicon-o-pencil-square')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Components\Select::make('category')
                            ->options(Voice::CATEGORIES)
                            ->required()
                            ->default('experience'),
                        Components\TextInput::make('slug')
                            ->helperText('Leave blank to generate from the title.')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Components\TextInput::make('title')
                            ->required()
                            ->maxLength(160)
                            ->columnSpanFull(),
                        Components\Textarea::make('body')
                            ->required()
                            ->rows(8)
                            ->columnSpanFull(),
                        Components\FileUpload::make('image_path')
                            ->label('Photo')
                            ->image()
                            ->directory('voices')
                            ->disk('public')
                            ->imageEditor()
                            ->columnSpanFull(),
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
                            ->default('pending'),
                        Components\Toggle::make('is_featured')
                            ->label('Featured')
                            ->helperText('Featured voices are pinned to the top of the wall.'),
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
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40)
                    ->description(fn (Voice $r) => $r->author_name . ($r->country ? ' · ' . $r->country : '')),
                Tables\Columns\TextColumn::make('category')
                    ->formatStateUsing(fn ($state) => Voice::CATEGORIES[$state] ?? $state)
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('likes_count')->label('♥')->sortable(),
                Tables\Columns\TextColumn::make('comments_count')->label('💬')->sortable(),
                Tables\Columns\TextColumn::make('views_count')->label('👁')->sortable()->toggleable(),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured')->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('category')->options(Voice::CATEGORIES),
            ])
            ->recordActions([
                Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Voice $r) => $r->status !== 'approved')
                    ->action(fn (Voice $r) => $r->update(['status' => 'approved'])),
                Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Voice $r) => $r->status !== 'rejected')
                    ->action(fn (Voice $r) => $r->update(['status' => 'rejected'])),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\BulkAction::make('approve_selected')
                        ->label('Approve selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['status' => 'approved'])),
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVoices::route('/'),
            'create' => CreateVoice::route('/create'),
            'edit' => EditVoice::route('/{record}/edit'),
        ];
    }
}
