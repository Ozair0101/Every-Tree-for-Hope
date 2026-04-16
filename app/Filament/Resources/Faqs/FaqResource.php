<?php

namespace App\Filament\Resources\Faqs;

use App\Filament\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Resources\Faqs\Pages\EditFaq;
use App\Filament\Resources\Faqs\Pages\ListFaqs;
use App\Models\Faq;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationLabel = 'FAQs';

    protected static ?string $modelLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'FAQs';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'question';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category')
                ->label('Category')
                ->options([
                    'Introduction & Background' => 'Introduction & Background',
                    'Goals & Mission' => 'Goals & Mission',
                    'Working Methods' => 'Working Methods',
                    'Team & Structure' => 'Team & Structure',
                    'Impact & Community' => 'Impact & Community',
                    'Future & Vision' => 'Future & Vision',
                    'Challenges' => 'Challenges',
                    'General' => 'General',
                ])
                ->searchable()
                ->required(),

            Textarea::make('question')
                ->label('Question')
                ->required()
                ->rows(3)
                ->columnSpanFull(),

            Textarea::make('answer')
                ->label('Answer')
                ->rows(6)
                ->columnSpanFull()
                ->helperText('Leave empty if not yet answered. Only FAQs with answers and verified=true are shown publicly.'),

            TextInput::make('asked_by_name')
                ->label('Asked By (Name)')
                ->maxLength(255),

            TextInput::make('asked_by_email')
                ->label('Asked By (Email)')
                ->email()
                ->maxLength(255),

            TextInput::make('sort_order')
                ->label('Sort Order')
                ->numeric()
                ->default(0),

            Toggle::make('is_verified')
                ->label('Verified & Published')
                ->helperText('Only verified FAQs with answers are shown on the public page')
                ->default(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('question')
                    ->limit(60)
                    ->searchable()
                    ->wrap()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('answer')
                    ->limit(50)
                    ->toggleable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('asked_by_name')
                    ->label('Asked By')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Published')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'Introduction & Background' => 'Introduction & Background',
                        'Goals & Mission' => 'Goals & Mission',
                        'Working Methods' => 'Working Methods',
                        'Team & Structure' => 'Team & Structure',
                        'Impact & Community' => 'Impact & Community',
                        'Future & Vision' => 'Future & Vision',
                        'Challenges' => 'Challenges',
                        'General' => 'General',
                    ]),
                Tables\Filters\SelectFilter::make('is_verified')
                    ->label('Status')
                    ->options(['1' => 'Published', '0' => 'Unpublished']),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFaqs::route('/'),
            'create' => CreateFaq::route('/create'),
            'edit' => EditFaq::route('/{record}/edit'),
        ];
    }
}
