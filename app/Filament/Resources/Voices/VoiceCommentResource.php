<?php

namespace App\Filament\Resources\Voices;

use App\Filament\Resources\Voices\Pages\ListVoiceComments;
use App\Models\VoiceComment;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class VoiceCommentResource extends Resource
{
    protected static ?string $model = VoiceComment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Comments';

    protected static string|\UnitEnum|null $navigationGroup = 'Community';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Comment';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\TextInput::make('author_name')->required()->maxLength(120),
                Components\Select::make('status')
                    ->options(['approved' => 'Visible', 'hidden' => 'Hidden'])
                    ->required()
                    ->default('approved'),
                Components\Textarea::make('body')->required()->rows(4)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('author_name')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('body')->limit(70)->wrap(),
                Tables\Columns\TextColumn::make('voice.title')->label('On voice')->limit(30)->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => $state === 'approved' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['approved' => 'Visible', 'hidden' => 'Hidden']),
            ])
            ->recordActions([
                Actions\Action::make('toggle')
                    ->label(fn (VoiceComment $r) => $r->status === 'approved' ? 'Hide' : 'Show')
                    ->icon('heroicon-o-eye-slash')
                    ->action(fn (VoiceComment $r) => $r->update([
                        'status' => $r->status === 'approved' ? 'hidden' : 'approved',
                    ])),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVoiceComments::route('/'),
        ];
    }
}
